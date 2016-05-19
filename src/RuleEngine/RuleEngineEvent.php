<?php

namespace Karambol\RuleEngine;

use Symfony\Component\EventDispatcher\Event;
use Karambol\RuleEngine\ExpressionFunctionProvider;


class RuleEngineEvent extends Event {

  const BEFORE_EXECUTE_RULES = 'rule_engine.before_execute';
  const AFTER_EXECUTE_RULES = 'rule_engine.after_execute';

  protected $provider;
  protected $vars;
  protected $rules;

  public function __construct(array $rules, array $vars = [], ExpressionFunctionProvider $provider = null) {
    $this->rules = $rules;
    $this->vars = $vars;
    $this->provider = $provider;
  }

  public function &getFunctionProvider() {
    return $this->provider;
  }

  public function &getVars() {
    return $this->vars;
  }

  public function getRules() {
    return $this->rules;
  }

}
