<?php

namespace Karambol\RuleEngine;

use Karambol\RuleEngine\ExpressionLanguage\SimpleExpressionFunctionProvider;
use Karambol\RuleEngine\Context\Context;
use Symfony\Component\EventDispatcher\Event;


class RuleEngineEvent extends Event {

  const BEFORE_EXECUTE_RULES = 'rule_engine.before_execute';
  const AFTER_EXECUTE_RULES = 'rule_engine.after_execute';

  protected $provider;
  protected $context;
  protected $rules;
  protected $type;

  public function __construct($type, array $rules, Context $context = null, SimpleExpressionFunctionProvider $provider = null) {
    $this->type = $type;
    $this->rules = $rules;
    $this->context = $context;
    $this->provider = $provider;
  }

  public function &getFunctionProvider() {
    return $this->provider;
  }

  public function &getContext() {
    return $this->context;
  }

  public function &getRules() {
    return $this->rules;
  }

  public function getType() {
    return $this->type;
  }

}
