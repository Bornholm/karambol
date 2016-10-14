<?php

namespace Karambol\RuleEngine\Backup\Transform;

use Karambol\RuleEngine\RuleInterface;

interface TransformerInterface {

  /**
   * Transform a rule to its serializable representation
   *
   * @param RuleInterface $rule
   * @return array The serizalizable view of the data
   */
  public function serialize(RuleInterface $rule);

  /**
   * Parse a serialized rule
   *
   * @param array $ruleData
   * @return RuleInterface The parsed rule
   */
  public function deserialize(array $ruleData);

}
