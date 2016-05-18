<?php

namespace Karambol\RuleEngine;

use Karambol\RuleEngine\RuleInterface;

class Rule implements RuleInterface {

  protected $condition;
  protected $action;

  public function __construct($condition, $action) {
    $this->condition = $condition;
    $this->action = $action;
  }

  public function getCondition() {
    return $this->condition;
  }

  public function getAction() {
    return $this->action;
  }

}
