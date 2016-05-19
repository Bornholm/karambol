<?php

namespace Karambol\RuleEngine;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Doctrine\ORM\EntityManager;
use Karambol\RuleEngine\RuleEngineAPI;
use Karambol\RuleEngine\RuleEngineEvent;
use Karambol\RuleEngine\ParserCache;

class RuleEngineService extends EventDispatcher {

  public function execute(array $rules, array $vars = null) {

    $language = new ExpressionLanguage(new ParserCache());
    $provider =  new ExpressionFunctionProvider();

    $event = new RuleEngineEvent($rules, $vars, $provider);
    $this->dispatch(RuleEngineEvent::BEFORE_EXECUTE_RULES, $event);

    $vars = $event->getVars() ? $event->getVars(): [];

    $language->registerProvider($provider);

    foreach($rules as $r) {
      $result = $language->evaluate($r->getCondition(), $vars);
      if($result === true) $language->evaluate($r->getAction(), $vars);
    }

    $this->dispatch(RuleEngineEvent::AFTER_EXECUTE_RULES, $event);

  }

}
