<?php

namespace Karambol\RuleEngine;

use Karambol\RuleEngine\RuleEngineEvent;
use Karambol\RuleEngine\RuleEngineService;
use Karambol\RuleEngine\Rule;

class DefaultCustomizationRulesListener {

  public function onBeforeExecuteRules(RuleEngineEvent $event) {

    if($event->getType() !== RuleEngineService::CUSTOMIZATION) return;

    $rules = &$event->getRules();

    $rules[] = new Rule('not isConnected()', 'addPageToMenu("login", "home_main", {"align":"right", "icon_class": "fa fa-sign-in"})');
    $rules[] = new Rule('isGranted("ROLE_ADMIN")', 'addPageToMenu("admin", "home_main", {"align":"right", "icon_class": "fa fa-wrench"})');
    $rules[] = new Rule('isConnected()', 'addPageToMenu("logout", "home_main", {"align":"right", "icon_class": "fa fa-sign-out"})');

  }

}
