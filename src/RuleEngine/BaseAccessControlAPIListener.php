<?php

namespace Karambol\RuleEngine;

use Karambol\RuleEngine\RuleEngineEvent;
use Karambol\RuleEngine\RuleEngineService;
use Karambol\KarambolApp;
use Karambol\Menu\MenuItem;
use Karambol\Page\PageInterface;
use Karambol\Page\Page;
use Karambol\Entity\BaseUser;

class BaseAccessControlAPIListener extends CommonAPIConfigurator {

  public function onBeforeExecuteRules(RuleEngineEvent $event) {

    if($event->getType() !== RuleEngineService::ACCESS_CONTROL) return;

    $app = $this->app;
    $provider = $event->getFunctionProvider();

    $this->registerCommonAPI($provider);

    $provider->registerFunction(
      'allow',
      function($vars, $action, $selector) use ($app) {
        $context = $vars['_context'];
        $context->authorizations[] = [
          'action' => $action,
          'selector' => $selector
        ];
      }
    );

  }

}
