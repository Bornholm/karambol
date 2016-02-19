<?php

namespace Karambol\RuleEngine;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Karambol\RuleEngine\Rule\RuleInterface;
use Karambol\RuleEngine\Action\ActionInterface;

class RuleEngine extends EventDispatcher {

  protected $rule;
  protected $actions;

  public function __construct() {
    $this->actions = new ArrayCollection();
  }

  public function setRule(RuleInterface $rule) {
    $this->rule = $rule;
    return $this;
  }

  public function getRule() {
    return $this->rule;
  }

  public function addAction(ActionInterface $action) {
    $this->actions->add($action);
    return $this;
  }

  public function removeAction(ActionInterface $action) {
    $this->actions->removeElement($action);
    return $this;
  }

  public function exec($subject) {
    $result = $this->rule->test($subject);
    if($result) {
      $actions = $this->actions;
      foreach($actions as $action) {
        $action->exec($subject);
      }
    }
    return $result;
  }


}