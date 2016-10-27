<?php

namespace Karambol\RuleEngine;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Doctrine\ORM\EntityManager;
use Karambol\RuleEngine\RuleEngineAPI;
use Karambol\RuleEngine\RuleEngineEvent;
use Karambol\RuleEngine\ExpressionLanguage\ParserCache;
use Karambol\RuleEngine\ExpressionLanguage\SimpleExpressionFunctionProvider;
use Karambol\RuleEngine\Context\Context;

class RuleEngine extends EventDispatcher {

  const CUSTOMIZATION = 'customization';
  const ACCESS_CONTROL = 'access_control';

  public function execute($type, array $rules, Context $context = null) {

    $language = new ExpressionLanguage(new ParserCache());
    $provider =  new SimpleExpressionFunctionProvider();

    $event = new RuleEngineEvent($type, $rules, $context, $provider);
    $this->dispatch(RuleEngineEvent::BEFORE_EXECUTE_RULES, $event);

    $context = $event->getContext();
    if($context === null) $context = new Context();

    $language->registerProvider($provider);

    foreach($event->getRules() as $r) {
      try {
        $result = $language->evaluate($r->getCondition(), $context->toArray());
      } catch(\Exception $ex) {
        throw new Exception\RuleConditionException($r, $ex);
      }
      if($result === true) {
        try {
          foreach($r->getActions() as $i => $action) {
            if(!empty($action)) $language->evaluate($action, $context->toArray());
          }
        } catch(\Exception $ex) {
          throw new Exception\RuleActionException($r, $i, $ex);
        }
      }
    }

    $this->dispatch(RuleEngineEvent::AFTER_EXECUTE_RULES, $event);

  }

}
