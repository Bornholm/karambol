<?php

namespace Karambol\Test\RuleEngine\Backup;

use Karambol\RuleEngine;
use Karambol\Entity\CustomRule;
use Karambol\RuleEngine\Rule;
use Karambol\RuleEngine\Backup\Serial;
use Karambol\RuleEngine\Backup\Deserializer;
use Karambol\RuleEngine\Backup\Transform\CustomRuleTransformer;
use Symfony\Component\Yaml\Yaml;

class DeserializerTest extends \PHPUnit_Framework_TestCase {

  public function testBasicRulesDeserialization() {

    $yaml = file_get_contents(__DIR__.'/../../Fixtures/rules.yml');

    $deserializer = new Deserializer();

    $rules = $deserializer->deserialize($yaml);

    $this->assertNotNull($rules);
    $this->assertCount(8, $rules);

    foreach($rules as $r) {
      $this->assertInstanceOf(RuleEngine\Rule::class, $r);
    }

  }

  public function testCustomRulesDeserialization() {

    $yaml = file_get_contents(__DIR__.'/../../Fixtures/rules.yml');

    $deserializer = new Deserializer();

    $rules = $deserializer->deserialize($yaml, new CustomRuleTransformer());

    $this->assertNotNull($rules);
    $this->assertCount(8, $rules);

    foreach($rules as $r) {
      $this->assertInstanceOf(CustomRule::class, $r);
    }

  }

}
