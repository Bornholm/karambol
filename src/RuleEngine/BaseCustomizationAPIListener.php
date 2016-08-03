<?php

namespace Karambol\RuleEngine;

use Karambol\RuleEngine\RuleEngineEvent;
use Karambol\RuleEngine\RuleEngine;
use Karambol\KarambolApp;
use Karambol\Menu\MenuItem;
use Karambol\Page\PageInterface;
use Karambol\Page\Page;
use Karambol\VirtualSet\ItemCountEvent;
use Karambol\VirtualSet\ItemSearchEvent;
use Karambol\AccessControl\Resource;
use Karambol\AccessControl\BaseActions;
use Karambol\AccessControl\Parser\ResourceSelectorParser;

class BaseCustomizationAPIListener extends CommonAPIConfigurator {

  public function onBeforeExecuteRules(RuleEngineEvent $event) {

    if($event->getType() !== RuleEngine::CUSTOMIZATION) return;

    $app = $this->app;

    $provider = $event->getFunctionProvider();

    $this->registerCommonAPI($provider);

    $provider->registerFunction(
      'addPageToMenu',
      function($vars, $pageSlug, $menuName, $itemAttrs = []) use ($app) {

        $menu = $app['menus']->getMenu($menuName);

        if($pageSlug instanceof PageInterface) {
          $page = $pageSlug;
        } else {
          $page = $app['pages']->findOne(['slug' => $pageSlug]);
        }

        if(!$page) return;

        $menuItem = new MenuItem($page->getLabel(), $page->getURL(), $itemAttrs);

        $menu->addListener(ItemSearchEvent::NAME, function(ItemSearchEvent $event) use ($menuItem) {
          $event->addItem($menuItem);
        });

        $menu->addListener(ItemCountEvent::NAME, function(ItemCountEvent $event) {
          $event->add(1);
        });

      }
    );

    $provider->registerFunction(
      'useTheme',
      function($vars, $themeName) use ($app) {
        $app['themes']->setSelectedTheme($themeName);
      }
    );

    $provider->registerFunction(
      'setHomepage',
      function($vars, $pageSlug) use ($app) {

        if($pageSlug instanceof PageInterface) {
          $page = $pageSlug;
        } else {
          $page = $app['pages']->findOne(['slug' => $pageSlug]);
        }

        if(!$page) return;

        $app['pages']->setHomepage($page);

      }
    );

    $provider->registerFunction(
      'asFrame',
      function($vars, $pageSlug) use ($app) {
        $urlGen = $app['url_generator'];
        $pageService = $app['pages'];
        $page = $pageService->findOne(['slug' => $pageSlug]);
        if(!$page) return;
        return new Page($page->getLabel(), $urlGen->generate('framed_page', ['pageSlug' => $pageSlug]), $pageSlug);
      }
    );

    $provider->registerFunction(
      'isGranted',
      function($vars, $actionOrRole, $resourceSelector = null) use ($app) {

        $authChecker = $app['security.authorization_checker'];

        if($resourceSelector === null) {
          $action = BaseActions::ROLE;
          $resource = new Resource('role', $actionOrRole);
          return $authChecker->isGranted($action, $resource);
        }

        $action = $actionOrRole;

        $parser = new ResourceSelectorParser();
        $selector = $parser->parse($resourceSelector);

        $resources = $selector->getAssociatedResources();

        foreach($resources as $resource) {
          if($authChecker->isGranted($action, $resource)) return true;
        }

        return false;

      }

    );

  }

}
