<?php

namespace Karambol\RuleEngine\Backup\Transform;

use Karambol\Entity\Ruleset;
use Karambol\Entity\CustomRule;
use Karambol\RuleEngine\Rule;
use Karambol\RuleEngine\RuleInterface;
use Karambol\RuleEngine\Backup\Transform\BasicTransformer;
use Karambol\RuleEngine\Backup\Transform\TransformerInterface;
use Karambol\RuleEngine\Backup\Transform\Exception\TransformerException;
use Karambol\RuleEngine\Backup\Transform\Exception\InvalidRuleFormatException;

class CustomRuleTransformer extends BasicTransformer {

  public function serialize(RuleInterface $rule) {

    if(!($rule instanceof CustomRule)) throw new TransformerException('The rule must be an instance of CustomRule !');

    $export = parent::serialize($rule);
    $export['origin'] = $rule->getOrigin();
    $export['weight'] = $rule->getWeight();
    $export['set'] = $rule->getRuleset()->getName();

    return $export;

  }

  public function deserialize(array $ruleData) {

    $rule = parent::deserialize($ruleData);

    if(!isset($ruleData['weight']) || !is_int($ruleData['weight'])) {
      throw new InvalidRuleFormatException(sprintf('The rule\'s weigth attribute is invalid or malformed ! Rule: %s', json_encode($rule)));
    }

    if(!isset($ruleData['origin']) || !is_string($ruleData['origin'])) {
      throw new InvalidRuleFormatException(sprintf('The rule\'s origin attribute is invalid or malformed ! Rule: %s', json_encode($rule)));
    }

    if(!isset($ruleData['set']) || !is_string($ruleData['set'])) {
      throw new InvalidRuleFormatException(sprintf('The rule\'s set attribute is invalid or malformed ! Rule: %s', json_encode($rule)));
    }

    $customRule = new CustomRule();
    $customRule->setActions($rule->getActions());
    $customRule->setCondition($rule->getCondition());
    $customRule->setOrigin($ruleData['origin']);
    $customRule->setWeight($ruleData['weight']);

    $ruleset = new Ruleset();
    $ruleset->setName($ruleData['set']);
    $customRule->setRuleset($ruleset);

    return $customRule;

  }


}
