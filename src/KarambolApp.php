<?php

namespace Karambol;

use Silex\Application;
use Karambol\Provider\YamlConfigServiceProvider;
use Karambol\Provider\DoctrineORMServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Karambol\Controller;

class KarambolApp extends Application
{

  public function __construct() {
    parent::__construct();
    $this->bootstrapApplication();
  }

  protected function bootstrapApplication() {
    $this->bootstrapConfig();
    $this->bootstrapDoctrine();
    $this->bootstrapMonolog();
    $this->bootstrapTwig();
    $this->bootstrapUrlGenerator();
    $this->bootstrapSecurity();
    $this->bootstrapControllers();
    $this->bootstrapPlugins();
  }

  protected function bootstrapConfig() {

    $configDir = __DIR__.'/../config';

    $defaultConfig = $configDir.'/default.yml';
    $this->register(new YamlConfigServiceProvider($defaultConfig));

    $hostConfig = $configDir.'/'.gethostname().'.yml';
    if(file_exists($hostConfig)) {
      $this->register(new YamlConfigServiceProvider($hostConfig));
    }

    $localConfig = $configDir.'/local.yml';
    if(file_exists($localConfig)) {
      $this->register(new YamlConfigServiceProvider($localConfig));
    }

    // Activate debug
    $this['debug'] = $this['config']['debug'];

  }

  protected function bootstrapDoctrine() {
    $databaseConfig = $this['config']['database'];
    $debug = $this['config']['debug'];
    $config['orm.entities'] = [__DIR__];
    $this->register(new DoctrineORMServiceProvider($config['orm.entities'], $databaseConfig, $debug));
  }

  protected function bootstrapMonolog() {

    $loggerConfig = $this['config']['logger'];

    $this->register(new MonologServiceProvider(), [
      'monolog.logfile' => !empty($loggerConfig['file']) ? $loggerConfig['file'] : __DIR__.'/../karambol.log',
      'monolog.level' => !empty($loggerConfig['level']) ? $loggerConfig['level'] : 'debug',
      'monolog.name' => 'karambol',
    ]);

  }

  protected function bootstrapTwig() {

    // Init Twig view engine
    $this->register(new TwigServiceProvider(), [
      'twig.path' => [__DIR__.'/../views'],
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

  protected function bootstrapControllers() {

    $homeCtrl = new Controller\HomeController();
    $homeCtrl->mount($this);

  }

  protected function bootstrapPlugins() {

    $plugins = $this['config']['plugins'];
    $logger = $this['monolog'];

    foreach($plugins as $pluginClass) {
      $logger->addDebug(sprintf('Load plugin "%s"', $pluginClass));
      $plugin = new $pluginClass();
      $plugin->boot($this);
    }

  }

}
