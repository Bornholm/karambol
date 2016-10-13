<?php

namespace Karambol\RuleEngine\Backup;

use Karambol\RuleEngine\Backup\Transform\BasicTransformer;
use Karambol\RuleEngine\Backup\Transform\TransformerInterface;
use Karambol\RuleEngine\Backup\Exception\InvalidFormatException;
use Symfony\Component\Yaml\Yaml;

class Deserializer {

  /**
   * Deserialize a rules dump
   *
   * @throws Symfony\Component\Yaml\Exception\ParseException
   *
   * @param string $dump The rules dump, in YAML format
   * @param TransformerInterface $transformer The transformer to use to hydrate the rules' data, default to BasicTransformer
   * @return array The parsed rules
   */
  public function deserialize($yaml, TransformerInterface $transformer = null) {

    $dump = Yaml::parse($yaml);

    $this->validate($dump);

    if($transformer === null) $transformer = new BasicTransformer();

    $rules = [];

    foreach($dump['rules'] as $ruleData) {
      $rules[] = $transformer->deserialize($ruleData);
    }

    return $rules;

  }

  /**
   * Validate a rules dump object structure
   *
   * @throws Karambol\RuleEngine\Exception\InvalidDumpFormatException
   *
   * @param string $dump The rules dump object
   * @return boolean
   */
  public function validate($dump) {

    if(!isset($dump['version']) || $dump['version'] !== Serial::VERSION) {
      throw new InvalidFormatException(sprintf('The dump\'s version is not defined or not supported !'));
    }

    if(!isset($dump['rules']) || !is_array($dump['rules'])) {
      throw new InvalidFormatException(sprintf('The dump\'s rules attribute is not defined or malformed !'));
    }

    return true;

  }

}
