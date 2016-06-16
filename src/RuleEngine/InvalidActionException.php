<?php

namespace Karambol\RuleEngine;

use Karambol\RuleEngine\RuleInterface;

class InvalidActionException extends \Exception
{

  protected $rule;
  protected $actionIndex;

  public function __construct(RuleInterface $rule, $actionIndex, \Exception $previous = null) {
    $this->actionIndex = $actionIndex;
    $this->rule = $rule;
    parent::__construct(sprintf('An error occured while executing "%s" action !', $this->getAction()), 0, $previous);
  }

  public function getRule() {
    return $this->rule;
  }

  public function getAction() {
    return $this->rule->getActions()[$this->getActionIndex()];
  }

  public function getActionIndex() {
    return $this->actionIndex;
  }

}
