<?php

namespace Karambol\RuleEngine;

use Karambol\RuleEngine\RuleEngineEvent;
use Karambol\RuleEngine\RuleEngineService;
use Karambol\KarambolApp;
use Karambol\Menu\MenuItem;
use Karambol\Page\PageInterface;
use Karambol\Page\Page;
use Karambol\Entity\BaseUser;
use Karambol\AccessControl\Parser\ResourceSelectorParser;
use Karambol\AccessControl\ResourceSelector;
use Karambol\AccessControl\ResourceOwnerInterface;
use Karambol\AccessControl\ResourceInterface;

class BaseAccessControlAPIListener extends CommonAPIConfigurator {

  public function onBeforeExecuteRules(RuleEngineEvent $event) {

    if($event->getType() !== RuleEngineService::ACCESS_CONTROL) return;

    $app = $this->app;
    $provider = $event->getFunctionProvider();

    $this->registerCommonAPI($provider);
    $provider->registerFunction('owns', [$this, 'ownsMethodHandler']);
    $provider->registerFunction('allow', [$this, 'allowMethodHandler']);

  }

  public function ownsMethodHandler($vars, $selectorOrResource) {

    $app = $this->app;
    $context = $vars['_context'];
    $user = $context->user;
    $resources = null;

    if(!($user instanceof ResourceOwnerInterface)) {
      throw new Error('The user instance doesn\'t implement the ResourceOwnerInterface !');
    }

    if(is_string($selectorOrResource)) {
      $parser = new ResourceSelectorParser();
      $selector = $parser->parse($selectorOrResource);
      $resources = $selector->getAssociatedResources();
    }

    if($selectorOrResource instanceof ResourceInterface) {
      $resources = [$selectorOrResource];
    }

    if(is_array($selectorOrResource)) {
      $resources = $selectorOrResource;
    }

    if($resources === null) {
      throw new Error('The selector argument must be a resource, valid resource selector or an array of ressources !');
    }

    foreach($resources as $res) {
      if($user->owns($res)) return true;
    }

    return false;

  }

  public function allowMethodHandler($vars, $action, $selectorOrResource) {

    $context = $vars['_context'];

    $context->authorizations[] = [
      'action' => $action,
      'selector' => is_string($selectorOrResource) ? $selectorOrResource : null,
      'resource' => $selectorOrResource instanceof ResourceInterface ? $selectorOrResource : null,
    ];

  }

}
