<?php

use Karambol\RuleEngine\RuleEngine;
use Karambol\RuleEngine\Rule\AlwaysTrueRule;
use Karambol\RuleEngine\Rule\AlwaysFalseRule;
use Karambol\RuleEngine\Rule\RuleSet;
use Karambol\RuleEngine\Rule\PropertyTestRule;

class BoolEngineTest extends PHPUnit_Framework_TestCase
{

    public function testAndRuleSetShouldReturnTrue()
    {
      $subject = [];

      $engine = new RuleEngine();
      $ruleSet = new RuleSet();

      $ruleSet->setOperator(RuleSet::AND_OPERATOR);
      $ruleSet->addRule(new AlwaysTrueRule());
      $ruleSet->addRule(new AlwaysTrueRule());
      $engine->setRule($ruleSet);

      $result = $engine->exec($subject);

      $this->assertTrue($result);
    }

    public function testAndRuleSetShouldReturnFalse()
    {
      $subject = [];

      $engine = new RuleEngine();
      $ruleSet = new RuleSet();

      $ruleSet->setOperator(RuleSet::AND_OPERATOR);
      $ruleSet->addRule(new AlwaysTrueRule());
      $ruleSet->addRule(new AlwaysFalseRule());
      $engine->setRule($ruleSet);

      $result = $engine->exec($subject);

      $this->assertFalse($result);

    }

    public function testXorRuleSetShouldReturnTrue()
    {

      $subject = [];

      $engine = new RuleEngine();
      $ruleSet = new RuleSet();

      $ruleSet->setOperator(RuleSet::XOR_OPERATOR);
      $ruleSet->addRule(new AlwaysTrueRule());
      $ruleSet->addRule(new AlwaysFalseRule());

      $engine->setRule($ruleSet);

      $result = $engine->exec($subject);

      $this->assertTrue($result);

    }

    public function testNeqPropertyTestRuleShouldReturnTrue()
    {

      $subject = new \stdClass();
      $subject->foo = 1;

      $engine = new RuleEngine();
      $rule = new PropertyTestRule();

      $rule->setPropertyPath('foo');
      $rule->setComparator(PropertyTestRule::NEQ);
      $rule->setCriteria(0);

      $engine->setRule($rule);

      $result = $engine->exec($subject);

      $this->assertTrue($result);

    }

    public function testMatchPropertyTestRuleShouldReturnFalse()
    {

      $subject = new \stdClass();
      $subject->foo = 'bar';

      $engine = new RuleEngine();
      $rule = new PropertyTestRule();

      $rule->setPropertyPath('foo');
      $rule->setComparator(PropertyTestRule::MATCH);
      $rule->setCriteria('/baz/');

      $engine->setRule($rule);

      $result = $engine->exec($subject);

      $this->assertFalse($result);

    }

}
