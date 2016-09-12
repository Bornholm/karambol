<?php

namespace Karambol\Test\RuleEngine;

use Karambol\KarambolApp;
use Karambol\RuleEngine\RuleEngine;
use Karambol\RuleEngine\BaseAccessControlAPIListener;
use Karambol\Entity\User;
use Karambol\AccessControl\Resource;
use Karambol\AccessControl\ResourceInterface;
use Karambol\AccessControl\ResourceOwnerInterface;
use Karambol\RuleEngine\Rule;

class AccessControlAPITest extends \PHPUnit_Framework_TestCase
{

  public function setUp() {

    $app = new KarambolApp();
    $app->boot();
    $this->app = $app;

    $context = new \stdClass();
    $context->authorizations = [];

    $this->context = $context;
    $this->ruleEngine = $app['rule_engine'];

  }

  public function testOwnsMethod() {

    $listener = new BaseAccessControlAPIListener($this->app);

    $user = new MightyUser();
    $resource = new Resource('my-resource', 1);

    $this->context->user = $user;

    $vars = [
      '_context' => $this->context,
      'user' =>  $user->createRuleEngineView(),
      'resource' => $resource
    ];

    $rules = [
      new Rule('owns(resource)', ["allow('*', resource)"])
    ];

    $this->ruleEngine->execute(RuleEngine::ACCESS_CONTROL, $rules, $vars);

    $this->assertCount(1, $this->context->authorizations);

  }

}

class MightyUser extends User {

  public function owns(ResourceInterface $resource) {
    return true;
  }

}
