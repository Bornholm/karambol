<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Provider;
use Karambol\RuleEngine;
use Karambol\RuleEngine\Rule;
use Karambol\RuleEngine\RuleEngineAPIFactory;
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
      new Rule('not api.isConnected()', 'api.addPageToMenu("login", "home_main", {"align":"right", "icon_class": "fa fa-sign-in"})'),
      new Rule('api.isConnected()', 'api.useTheme("yeti")'),
      new Rule('api.isGranted("ROLE_ADMIN")', 'api.addPageToMenu("administration", "home_main", {"align":"right", "icon_class": "fa fa-wrench"})'),
      new Rule('api.isConnected()', 'api.addPageToMenu("logout", "home_main", {"align":"right", "icon_class": "fa fa-sign-out"})'),
      new Rule('api.isConnected()', 'api.addPageToMenu("linux-fr", "home_content")'),
      new Rule('api.isConnected()', 'api.addPageToMenu(api.asFrame("linux-fr"), "home_content", {"target": "_self"})')
    ];

    $vars = [
      'user' => $app['user']
    ];

    $apiFactory = $this->getPersonalizationAPIFactory();

    $ruleEngine->execute($rules, $apiFactory, $vars);

  }

  protected function getPersonalizationAPIFactory() {

    $app = $this->app;
    $apiFactory = new RuleEngine\RuleEngineAPIFactory();

    $apiFactory->registerMethod('isConnected', function() use ($app){
      return $app['user'] !== null;
    });

    $apiFactory->registerMethod('isGranted', function($authorization) use ($app){
      $authCheck = $app['security.authorization_checker'];
      return $authCheck->isGranted($authorization);
    });

    $apiFactory->registerMethod('addPageToMenu', function($pageSlug, $menuName, $menuItemAttrs = []) use ($app){

      $pageService = $app['page'];
      $menuService = $app['menu'];

      $menu = $menuService->getMenu($menuName);

      if($pageSlug instanceof PageInterface) {
        $page = $pageSlug;
      } else {
        $page = $pageService->findPageBySlug($pageSlug);
      }

      if(!$page) return;

      $menuItem = new MenuItem($page->getLabel(), $page->getURL(), $menuItemAttrs);
      $menu->addItem($menuItem);

    });

    $apiFactory->registerMethod('asFrame', function($pageSlug) use ($app){

      $urlGen = $app['url_generator'];
      $pageService = $app['page'];

      $page = $pageService->findPageBySlug($pageSlug);

      if(!$page) return;

      return new Page($page->getLabel(), $urlGen->generate('framed-page', ['pageSlug' => $pageSlug]));

    });

    $apiFactory->registerMethod('useTheme', function($themeName) use ($app){
      $themeService = $app['theme'];
      $themeService->setSelectedTheme($themeName);
    });

    $this->registerCommonAPI($apiFactory);

    return $apiFactory;

  }

  protected function registerCommonAPI(RuleEngineAPIFactory $apiFactory) {

    $app = $this->app;

    $apiFactory->registerMethod('log', function($message) use ($app){
      $logger = $app['monolog'];
      $logger->info($message);
    });

    return $apiFactory;

  }

}
