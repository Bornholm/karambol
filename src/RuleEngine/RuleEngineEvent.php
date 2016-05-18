<?php

namespace Karambol\RuleEngine;

use Symfony\Component\EventDispatcher\Event;
use Karambol\RuleEngine\RuleEngineAPIFactory;


class RuleEngineEvent extends Event {

  const BEFORE_EXECUTE_RULES = 'rule_engine.before_execute';
  const AFTER_EXECUTE_RULES = 'rule_engine.after_execute';

  protected $apiFactory;
  protected $vars;
  protected $rules;

  public function __construct(array $rules, RuleEngineAPIFactory $apiFactory, array $vars = []) {
    $this->rules = $rules;
    $this->vars = $vars;
    $this->apiFactory = $apiFactory;
  }

  public function getAPIFactory() {
    return $this->apiFactory;
  }

  public function getVars() {
    return $this->vars;
  }

  public function getRules() {
    return $this->rules;
  }

}
