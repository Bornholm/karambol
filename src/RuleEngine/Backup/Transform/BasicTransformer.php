<?php

namespace Karambol\RuleEngine\Backup\Transform;

use Karambol\RuleEngine\Rule;
use Karambol\RuleEngine\RuleInterface;
use Karambol\RuleEngine\Backup\Transform\TransformerInterface;

class BasicTransformer implements TransformerInterface {

  public function serialize(RuleInterface $rule) {
    return [
      'condition' => $rule->getCondition(),
      'actions' => $rule->getActions()
    ];
  }

  public function deserialize(array $ruleData) {

    if(!isset($ruleData['condition']) || !is_string($ruleData['condition'])) {
      throw new InvalidRuleFormatException(sprintf('The rule\'s condition attribute is invalid or malformed ! Rule: %s', json_encode($ruleData)));
    }

    if(!isset($ruleData['actions']) || !is_array($ruleData['actions'])) {
      throw new InvalidRuleFormatException(sprintf('The rule\'s actions attribute is invalid or malformed ! Rule: %s', json_encode($ruleData)));
    }

    $rule = new Rule($ruleData['condition'], $ruleData['actions']);

    return $rule;

  }


}
