<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Provider;
use Karambol\RuleEngine;
use Karambol\RuleEngine\Rule;
use Karambol\RuleEngine\RuleEngineEvent;
use Karambol\RuleEngine\ExpressionFunctionProvider;
use Symfony\Component\HttpFoundation\Request;
use Silex\Application;
use Karambol\Entity\RuleSet;
use Karambol\Menu\MenuItem;
use Karambol\Page\PageInterface;
use Karambol\Page\Page;

class RuleEngineBootstrap implements BootstrapInterface {

  protected $app;

  public function bootstrap(KarambolApp $app) {

    $this->app = $app;

    // Register rule engine service
    $app->register(new Provider\RuleEngineServiceProvider());
    $app->before([$this, 'applyCustomizationRules']);

  }

  public function applyCustomizationRules(Request $request, Application $app) {

    $logger = $app['monolog'];
    $ruleEngine = $app['rule_engine'];
    $rulesetRepo = $app['orm']->getRepository('Karambol\Entity\RuleSet');

    $ruleset = $rulesetRepo->findOneByName(RuleSet::CUSTOMIZATION);

    $baseRules = $this->getBaseRules($app, RuleSet::CUSTOMIZATION);

    if($ruleset) {
      $rules = array_merge($baseRules, $ruleset->getRules()->toArray());
    } else {
      $rules = $baseRules;
    }

    $vars = [
      'user' => $app['user']
    ];

    $configureCustomizationAPI =  [$this, 'configureCustomizationAPI'];
    $ruleEngine->addListener(RuleEngineEvent::BEFORE_EXECUTE_RULES, $configureCustomizationAPI);

    try {
      $ruleEngine->execute($rules, $vars);
    } catch(\Exception $ex) {
      $logger->error($ex);
    }

    $ruleEngine->removeListener(RuleEngineEvent::BEFORE_EXECUTE_RULES, $configureCustomizationAPI);

  }

  public function getBaseRules(Application $app, $rulesetName) {
    $config = $app['config'];
    $rules = [];
    if( isset($config['base_rules'][$rulesetName]) ) {
      $configBaseRules = $config['base_rules'][$rulesetName];
      foreach($configBaseRules as $ruleData) {
        $rules[] = new Rule($ruleData['condition'], $ruleData['action']);
      }
    }
    return $rules;
  }

  public function configureCustomizationAPI(RuleEngineEvent $event) {

    $app = $this->app;

    $provider = $event->getFunctionProvider();

    $provider->registerFunction(
      'isGranted',
      function() { return 'throw new \Exception(\'This expression is not meant to be compiled !\')'; },
      function($vars, $authorization) use ($app) {
        return $app['security.authorization_checker']->isGranted($authorization);
      }
    );

    $provider->registerFunction(
      'isConnected',
      function() { return 'throw new \Exception(\'This expression is not meant to be compiled !\')'; },
      function($vars) use ($app) {
        return $app['user'] !== null;
      }
    );

    $provider->registerFunction(
      'addPageToMenu',
      function() { return 'throw new \Exception(\'This expression is not meant to be compiled !\')'; },
      function($vars, $pageSlug, $menuName, $menuItemAttrs = []) use ($app) {

        $menu = $app['menu']->getMenu($menuName);

        if($pageSlug instanceof PageInterface) {
          $page = $pageSlug;
        } else {
          $page = $app['page']->findPageBySlug($pageSlug);
        }

        if(!$page) return;

        $menuItem = new MenuItem($page->getLabel(), $page->getURL(), $menuItemAttrs);
        $menu->addItem($menuItem);

        return $menuItem;

      }
    );

    $provider->registerFunction(
      'useTheme',
      function() { return 'throw new \Exception(\'This expression is not meant to be compiled !\')'; },
      function($vars, $themeName) use ($app) {
        $app['theme']->setSelectedTheme($themeName);
      }
    );

    $provider->registerFunction(
      'asFrame',
      function() { return 'throw new \Exception(\'This expression is not meant to be compiled !\')'; },
      function($vars, $pageSlug) use ($app) {
        $urlGen = $app['url_generator'];
        $pageService = $app['page'];
        $page = $pageService->findPageBySlug($pageSlug);
        if(!$page) return;
        return new Page($page->getLabel(), $urlGen->generate('framed-page', ['pageSlug' => $pageSlug]));
      }
    );

    $provider->registerFunction(
      'log',
      function() { return 'throw new \Exception(\'This expression is not meant to be compiled !\')'; },
      function($vars, $message) use ($app) {
        $logger = $app['monolog'];
        $logger->info($message);
      }
    );

  }

}
