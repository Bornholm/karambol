<?php

namespace Karambol\RuleEngine\Exception;

use Karambol\RuleEngine\RuleInterface;

class RuleConditionException extends \Exception
{

  protected $rule;

  public function __construct(RuleInterface $rule, \Exception $previous = null) {
    $this->rule = $rule;
    parent::__construct(sprintf('An error occured while checking "%s" condition !', $this->getCondition()), 0, $previous);
  }

  public function getRule() {
    return $this->rule;
  }

  public function getCondition() {
    return $this->rule->getCondition();
  }

}
