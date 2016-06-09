<?php

namespace Karambol\RuleEngine;

use Karambol\RuleEngine\RuleEngineEvent;
use Karambol\RuleEngine\RuleEngineService;
use Karambol\KarambolApp;
use Karambol\Menu\MenuItem;
use Karambol\Page\PageInterface;
use Karambol\Page\Page;

class DefaultCustomizationAPIListener {

  protected $app;

  public function __construct(KarambolApp $app) {
    $this->app = $app;
  }

  public function onBeforeExecuteRules(RuleEngineEvent $event) {

    if($event->getType() !== RuleEngineService::CUSTOMIZATION) return;

    $app = $this->app;

    $provider = $event->getFunctionProvider();

    $provider->registerFunction(
      'isGranted',
      function($vars, $authorization) use ($app) {
        return $app['security.authorization_checker']->isGranted($authorization);
      }
    );

    $provider->registerFunction(
      'isConnected',
      function($vars) use ($app) {
        return $app['user'] !== null;
      }
    );

    $provider->registerFunction(
      'addPageToMenu',
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
      function($vars, $themeName) use ($app) {
        $app['theme']->setSelectedTheme($themeName);
      }
    );

    $provider->registerFunction(
      'setHomepage',
      function($vars, $pageSlug) use ($app) {

        if($pageSlug instanceof PageInterface) {
          $page = $pageSlug;
        } else {
          $page = $app['page']->findPageBySlug($pageSlug);
        }

        if(!$page) return;

        $app['page']->setHomepage($page);

      }
    );

    $provider->registerFunction(
      'asFrame',
      function($vars, $pageSlug) use ($app) {
        $urlGen = $app['url_generator'];
        $pageService = $app['page'];
        $page = $pageService->findPageBySlug($pageSlug);
        if(!$page) return;
        return new Page($page->getLabel(), $urlGen->generate('framed_page', ['pageSlug' => $pageSlug]), $pageSlug);
      }
    );

    $provider->registerFunction(
      'log',
      function($vars, $message) use ($app) {
        $logger = $app['monolog'];
        $logger->info($message);
      }
    );

  }

}
