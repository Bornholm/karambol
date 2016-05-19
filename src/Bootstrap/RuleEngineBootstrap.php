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
    $app->before([$this, 'applyPersonalizationRules']);

  }

  public function applyPersonalizationRules(Request $request, Application $app) {

    $ruleEngine = $app['rule_engine'];
    $rulesetRepo = $app['orm']->getRepository('Karambol\Entity\RuleSet');

    $ruleset = $rulesetRepo->findOneByName(RuleSet::PERSONALIZATION);
    //if(!$ruleset) return;

    $rules = [
      new Rule('not isConnected()', 'addPageToMenu("login", "home_main", {"align":"right", "icon_class": "fa fa-sign-in"})'),
      new Rule('isConnected()', 'useTheme("yeti")'),
      new Rule('isGranted("ROLE_ADMIN")', 'addPageToMenu("administration", "home_main", {"align":"right", "icon_class": "fa fa-wrench"})'),
      new Rule('isConnected()', 'addPageToMenu("logout", "home_main", {"align":"right", "icon_class": "fa fa-sign-out"})'),
      new Rule('isConnected()', 'addPageToMenu("linux-fr", "home_content")'),
      new Rule('isConnected()', 'addPageToMenu(asFrame("linux-fr"), "home_content", {"target": "_self"})')
    ];

    //$rules = $ruleset->getRules();

    $vars = [
      'user' => $app['user']
    ];

    $configurePersonalizationAPI =  [$this, 'configurePersonalizationAPI'];
    $ruleEngine->addListener(RuleEngineEvent::BEFORE_EXECUTE_RULES, $configurePersonalizationAPI);
    $ruleEngine->execute($rules, $vars);
    $ruleEngine->removeListener(RuleEngineEvent::BEFORE_EXECUTE_RULES, $configurePersonalizationAPI);

  }

  public function configurePersonalizationAPI(RuleEngineEvent $event) {

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
