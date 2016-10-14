<?php

namespace Karambol\RuleEngine\Backup;

use Karambol\RuleEngine\Rule;
use Symfony\Component\Yaml\Yaml;
use Karambol\RuleEngine\Backup\Serial;
use Karambol\RuleEngine\Backup\Transform\TransformerInterface;
use Karambol\RuleEngine\Backup\Transform\BasicTransformer;

class Serializer {

  /** @var array */
  protected $rules = [];

  /**
   * Add rules to the set to serialize
   *
   * @param array $rules The array of rules
   * @return $this
   */
  public function addRules(array $rules) {
    $this->rules = array_merge($this->rules, $rules);
    return $this;
  }

  /**
   * Serialize the rules to YAML
   *
   * @param TransformerInterface $transformer The transformer to use to serizalize the rules
   * @return string
   */
  public function serialize(TransformerInterface $transformer = null) {

    if($transformer === null) $transformer = new BasicTransformer();

    $dump = [
      'version' => Serial::VERSION,
      'rules' => []
    ];

    foreach($this->rules as $rule) {
      $dump['rules'][] = $transformer->serialize($rule);
    }

    $now = new \DateTime('now');
    $yaml = sprintf('# Karambol rules -- %s'.PHP_EOL, $now->format('Y-m-d H:i:s'));
    $yaml .= Yaml::dump($dump, 3, 2);

    return $yaml;

  }

}
