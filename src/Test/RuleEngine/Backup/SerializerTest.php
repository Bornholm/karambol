<?php

namespace Karambol\Test\RuleEngine\Backup;

use Karambol\RuleEngine\Rule;
use Karambol\RuleEngine\Backup\Serial;
use Karambol\RuleEngine\Backup\Serializer;
use Symfony\Component\Yaml\Yaml;

class SerializerTest extends \PHPUnit_Framework_TestCase {

  public function testBasicRulesSerialization() {

    $rules = [
      new Rule('true', ['setHomepage(\'home\')']),
      new Rule('user.id == 5', ['allow(\'*\', \'*\')'])
    ];

    $serializer = new Serializer();
    $serializer->addRules($rules);

    $yaml = $serializer->serialize();

    $dump = Yaml::parse($yaml);

    $this->assertArrayHasKey('version', $dump);
    $this->assertEquals(Serial::VERSION, $dump['version']);
    $this->assertNotNull($dump['rules']);
    $this->assertCount(2, $dump['rules']);

  }

}
