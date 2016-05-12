<?php

namespace Karambol;

use Silex\Application;
use Karambol\Provider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Karambol\Controller;
use Karambol\Listener\AdminMenuListener;
use Karambol\Menu\MenuService;
use Karambol\Bootstrap;

class KarambolApp extends Application
{

  public function __construct() {
    parent::__construct();
    $this->bootstrapApplication();
  }

  protected function bootstrapApplication() {

    $configBootstrap = new Bootstrap\ConfigBootstrap();
    $configBootstrap->bootstrap($this);

    $doctrineBootstrap = new Bootstrap\DoctrineBootstrap();
    $doctrineBootstrap->bootstrap($this);

    $monologBootstrap = new Bootstrap\MonologBootstrap();
    $monologBootstrap->bootstrap($this);

    $ruleEngineBootstrap = new Bootstrap\RuleEngineBootstrap();
    $ruleEngineBootstrap->bootstrap($this);

    $this->bootstrapFormAndValidator();
    $this->bootstrapTwig();
    $this->bootstrapTheme();
    $this->bootstrapMenu();
    $this->bootstrapUrlGenerator();
    $this->bootstrapSecurity();
    $this->bootstrapTranslation();
    $this->bootstrapControllers();
    $this->bootstrapPlugins();
  }

  protected function bootstrapFormAndValidator() {
    $this->register(new ValidatorServiceProvider());
    $this->register(new FormServiceProvider());
  }

  protected function bootstrapTwig() {

    // Init Twig view engine
    $this->register(new TwigServiceProvider(), [
      'twig.path' => [__DIR__.'/Views'],
      'twig.form.templates' => ['bootstrap_3_layout.html.twig']
    ]);

    $this['twig'] = $this->share($this->extend('twig', function($twig, $app) {

      $twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) use ($app) {
        $req = $app['request'];
        $baseUrl = $req->getBasePath();
        return sprintf($baseUrl.'/%s', ltrim($asset, '/'));
      }));

      return $twig;

    }));

  }


  protected function bootstrapTheme() {
    // Register theme service
    $this->register(new Provider\ThemeServiceProvider());
    $themeListener = new Listener\ThemeListener($this);
    $this['theme']->addListener(
      Theme\ThemeService::THEME_CHANGE,
      [$themeListener, 'onThemeChange']
    );
    $this['theme']->setSelectedTheme(null);
  }

  protected function bootstrapMenu() {

    // Register menu service
    $this->register(new Provider\MenuServiceProvider());

    // Init default menu listeners
    $adminMenuListener = new AdminMenuListener();

    $this['menu']->addListener(
      MenuService::getMenuEvent('admin_main'),
      [$adminMenuListener, 'onMainMenuRender']
    );

  }

  protected function bootstrapSecurity() {
    $this->register(new SecurityServiceProvider(), [
      'security.firewalls' => [
        'admin' => [
          'pattern' => '^/admin',
          'http' => true,
          'users' => [
              // raw password is foo
              'admin' => ['ROLE_ADMIN', '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg=='],
          ]
        ]
      ]
    ]);
  }

  protected function bootstrapUrlGenerator() {
    $this->register(new UrlGeneratorServiceProvider());
  }

  protected function bootstrapTranslation() {
    $this->register(new TranslationServiceProvider(), [
      'locale' => 'fr',
      'locale_fallbacks' => array('en')
    ]);
    $this['translator'] = $this->share($this->extend('translator', function($translator) {
      $translator->addLoader('yaml', new YamlFileLoader());
      $baseDir = __DIR__.'/../locales';
      $transFiles = glob($baseDir.'/*.yml');
      foreach($transFiles as $file) {
        $lang = basename($file, '.yml');
        $translator->addResource('yaml', $file, $lang);
      }
      return $translator;
    }));
  }

  protected function bootstrapControllers() {

    // Homepage controllers
    $homeCtrl = new Controller\HomeController();
    $homeCtrl->bindTo($this);

    // Admin controllers
    $adminCtrl = new Controller\Admin\AdminController();
    $adminCtrl->bindTo($this);

    $adminUsersCtrl = new Controller\Admin\UsersController();
    $adminUsersCtrl->bindTo($this);

  }

  protected function bootstrapPlugins() {

    $plugins = $this['config']['plugins'];
    $logger = $this['monolog'];

    foreach($plugins as $pluginId => $pluginInfo) {
      if( isset($pluginInfo['class']) ) {
        $logger->debug(sprintf('Load plugin "%s" with class %s', $pluginId, $pluginInfo['class']));
        $pluginClass = $pluginInfo['class'];
        $plugin = new $pluginClass();
        $plugin->boot($this, isset($pluginInfo['options']) ? $pluginInfo['options'] : []);
      } else {
        $logger->warn(sprintf('Cannot load plugin "%s". No class specified', $pluginId));
      }
    }

  }

}
