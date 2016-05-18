<?php

namespace Karambol\RuleEngine;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Doctrine\ORM\EntityManager;
use Karambol\RuleEngine\RuleEngineAPI;
use Karambol\RuleEngine\RuleEngineEvent;

class RuleEngineService extends EventDispatcher {

  public function execute(array $rules, RuleEngineAPIFactory $apiFactory, array $vars = null) {

    $language = new ExpressionLanguage();

    $event = new RuleEngineEvent($rules, $apiFactory, $vars);
    $this->dispatch(RuleEngineEvent::BEFORE_EXECUTE_RULES, $event);

    $context = array_merge(is_array($event->getVars()) ? $event->getVars(): [], ['api' => $event->getAPIFactory()->getAPI()]);

    foreach($rules as $r) {
      $result = $language->evaluate($r->getCondition(), $context);
      if($result === true) $language->evaluate($r->getAction(), $context);
    }

    $this->dispatch(RuleEngineEvent::AFTER_EXECUTE_RULES, $event);

  }

}
