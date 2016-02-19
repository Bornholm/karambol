<?php

namespace Karambol\RuleEngine\Rule;

use Doctrine\Common\Collections\ArrayCollection;

class RuleSet implements RuleInterface {

  const AND_OPERATOR = 0;
  const OR_OPERATOR = 1;
  const XOR_OPERATOR = 2;

  protected $rules;
  protected $operator;

  public function __construct() {
    $this->operator = self::AND_OPERATOR;
    $this->rules = new ArrayCollection();
  }

  public function addRule(RuleInterface $rule) {
    $this->rules->add($rule);
    return $this;
  }

  public function removeRule(RuleInterface $rule) {
    $this->rules->removeElement($rule);
    return $this;
  }

  public function getRules() {
    return $this->rules;
  }

  public function setOperator($operator) {
    $this->operator = $operator;
    return $this;
  }

  public function getOperator() {
    return $this->operator;
  }

  public function setOptions(array $options) {

    if(isset($options['operator'])) {
      $this->setOperator($options['operator']);
    }

    if(isset($options['rules'])) {
      $rules = $options['rules'];
      foreach($rules as $ruleInfo) {
        $ruleClass = $ruleInfo['class'];
        $rule = new $ruleClass();
        if(isset($ruleInfo['options'])) {
          $rule->setOptions($ruleInfo['options']);
        }
        $this->addRule($rule);
      }
    }

  }

  public function test($subject) {

    $rules = $this->getRules();
    $operator = $this->getOperator();
    $result = null;

    foreach($rules as $rule) {
      $currentResult = $rule->test($subject);
      if($result === null) {
        $result = $currentResult;
        continue;
      }
      switch($operator) {
        case self::AND_OPERATOR:
          $result = $currentResult and $result;
          break;
        case self::OR_OPERATOR:
          $result = $currentResult or $result;
          break;
        case self::XOR_OPERATOR:
          $result = !!($currentResult xor $result);
          break;
        default:
          throw new \Exception(sprintf('Unknown operator "%s" !', $operator));
          break;
      }

    }

    return $result;

  }

}