<?php

namespace Karambol\RuleEngine;

use Karambol\RuleEngine\RuleEngineEvent;
use Karambol\RuleEngine\RuleEngineService;
use Karambol\KarambolApp;
use Karambol\Menu\MenuItem;
use Karambol\Page\PageInterface;
use Karambol\Page\Page;
use Karambol\Entity\User;

class BaseAccessControlAPIListener extends CommonAPIConfigurator {

  public function onBeforeExecuteRules(RuleEngineEvent $event) {

    if($event->getType() !== RuleEngineService::ACCESS_CONTROL) return;

    $app = $this->app;
    $provider = $event->getFunctionProvider();

    $this->registerCommonAPI($provider);

    $provider->registerFunction(
      'addRole',
      function($vars, $role) use ($app) {
        if(isset($vars['_user']) && $vars['_user'] instanceof User) $vars['_user']->addRole($role);
      }
    );

    $provider->registerFunction(
      'removeRole',
      function($vars, $role) use ($app) {
        if(isset($vars['_user']) && $vars['_user'] instanceof User) $vars['_user']->removeRole($role);
      }
    );

  }

}
