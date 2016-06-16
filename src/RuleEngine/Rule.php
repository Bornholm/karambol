<?php

namespace Karambol\RuleEngine;

use Karambol\RuleEngine\RuleInterface;

class Rule implements RuleInterface {

  protected $condition;
  protected $actions;

  public function __construct($condition, array $actions) {
    $this->condition = $condition;
    $this->actions = $actions;
  }

  public function getCondition() {
    return $this->condition;
  }

  public function getActions() {
    return $this->actions;
  }

}
