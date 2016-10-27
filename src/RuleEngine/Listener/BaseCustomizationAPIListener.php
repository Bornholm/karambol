<?php

namespace Karambol\RuleEngine\Listener;

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
use Karambol\RuleEngine\Listener\CommonAPI;
use Karambol\AccessControl\ResourceInterface;
use Karambol\AccessControl\Parser\ResourceSelectorParser;

class BaseCustomizationAPIListener {

  use \Karambol\RuleEngine\Listener\CommonAPITrait;

  public function onBeforeExecuteRules(RuleEngineEvent $event) {

    if($event->getType() !== RuleEngine::CUSTOMIZATION) return;
    $app = $this->app;
    $provider = $event->getFunctionProvider();

    $this->registerCommonAPI($provider);

    $provider->registerFunction('addPageToMenu', [$this, 'addPageToMenu']);
    $provider->registerFunction('useTheme', [$this, 'useTheme']);
    $provider->registerFunction('setHomepage', [$this, 'setHomepage']);
    $provider->registerFunction('asFrame', [$this, 'asFrame']);
    $provider->registerFunction('resource', [$this, 'resource']);
    $provider->registerFunction('isGranted', [$this, 'isGranted']);

  }

  public function addPageToMenu($vars, $pageSlug, $menuName, $itemAttrs = []) {

    $app = $this->app;
    $pageSlug = $this->unwrap($pageSlug);
    $menuName = $this->unwrap($menuName);
    $itemAttrs = $this->unwrap($itemAttrs);

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

  public function useTheme($vars, $themeName) {
    $this->app['themes']->setSelectedTheme($this->unwrap($themeName));
  }

  public function setHomepage($vars, $pageSlug) {

    $pages = $this->app['pages'];
    $pageSlug = $this->unwrap($pageSlug);

    if($pageSlug instanceof PageInterface) {
      $page = $pageSlug;
    } else {
      $page = $pages->findOne(['slug' => $pageSlug]);
    }

    if(!$page) return;

    $pages->setHomepage($page);

  }

  public function asFrame($vars, $pageSlug) {

    $urlGen = $this->app['url_generator'];
    $pageService = $this->app['pages'];
    $pageSlug = $this->unwrap($pageSlug);

    $page = $pageService->findOne(['slug' => $pageSlug]);

    if(!$page) return;

    return new Page($page->getLabel(), $urlGen->generate('framed_page', ['pageSlug' => $pageSlug]), $pageSlug);

  }

  public function resource($vars, $resourceType, $resourceId, $resourceProperty = null) {
    return new Resource($resourceType, $resourceId, $resourceProperty);
  }

  public function isGranted($vars, $actionOrRole, $resource = null) {

    $authChecker = $this->app['security.authorization_checker'];
    $actionOrRole = $this->unwrap($actionOrRole);
    $resource = $this->unwrap($resource);

    if($resource instanceof ResourceInterface) {
      $resources = [$resource];
    } else if(is_string($resource)) {
      $parser = new ResourceSelectorParser();
      $selector = $parser->parse($resource);
      dump($selector);
      $resources = [];
      $resourceType = $selector->getResourceType();

      $references = $selector->getResourceReferences();

      if(count($references) === 0) {
        $resources[] = new Resource($resourceType, '*');
      } else {
        foreach($references as $ref) {
          $resources[] = new Resource(
            $resourceType,
            $ref,
            $selector->getResourcePropertyName()
          );
        }
      }




    } else if($resource === null) {
      $action = BaseActions::ROLE;
      $resources = [new Resource('role', $actionOrRole)];
    } else {
      throw new \Exception('The $resource argument must null, a valid resource selector or an instance of ResourceInterface !');
    }

    foreach($resources as $res) {
      $isAllowed = $authChecker->isGranted($actionOrRole, $res);
      if($isAllowed) return true;
    }

    return false;

  }

}
