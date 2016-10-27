<?php

namespace Karambol\Test\RuleEngine;

use Karambol\KarambolApp;
use Karambol\RuleEngine\RuleEngine;
use Karambol\RuleEngine\Listener\BaseAccessControlAPIListener;
use Karambol\Entity\User;
use Karambol\AccessControl\Resource;
use Karambol\AccessControl\ResourceInterface;
use Karambol\AccessControl\ResourceOwnerInterface;
use Karambol\RuleEngine\Rule;
use Karambol\AccessControl\RuleEngine\AccessControlContext;
use Karambol\AccessControl\Permission\PermissionCollection;
use Karambol\RuleEngine\Context\Context;

class AccessControlAPITest extends \PHPUnit_Framework_TestCase
{

  use \Karambol\Test\KarambolTestTrait { setUp as karambolSetUp; }

  public function setUp() {
    $this->karambolSetUp();
    $this->ruleEngine = $this->app['rule_engine'];
  }

  public function testOwnsAllowMethod() {

    $listener = new BaseAccessControlAPIListener($this->app);

    $userStub = $this->createPartialMock(User::class, array('getId'));
    $userStub->method('getId')->willReturn(1);

    $resource = new Resource('user', 1);

    $context = new Context();
    $context->expose('user', $userStub);
    $context->expose('resource', $resource);
    $context->expose('_authorizations', new PermissionCollection());
    $context->expose('_rejections', new PermissionCollection());

    $rules = [
      new Rule('owns(resource)', ["allow('*', resource)"])
    ];

    $this->ruleEngine->execute(RuleEngine::ACCESS_CONTROL, $rules, $context);

    $authorizations = $context->getVariable('_authorizations')->getSource();

    $this->assertNotNull($authorizations);
    $this->assertCount(1, $authorizations);

    $auth = $authorizations[0];

    $this->assertArraySubset([
      'resource' => $resource,
      'action' => '*'
    ], $auth);

  }

  public function testOwnsDenyMethod() {

    $listener = new BaseAccessControlAPIListener($this->app);

    $userId = 1;
    $userStub = $this->getUserStub($userId);

    $notUserId = 2;
    $resource = new Resource('user', $notUserId, 'password');

    $context = new Context();
    $context->expose('user', $userStub);
    $context->expose('resource', $resource);
    $context->expose('_authorizations', new PermissionCollection());
    $context->expose('_rejections', new PermissionCollection());

    $rules = [
      new Rule('!owns(resource)', ["deny('*', resource)"])
    ];

    $this->ruleEngine->execute(RuleEngine::ACCESS_CONTROL, $rules, $context);

    $rejections = $context->getVariable('_rejections')->getSource();

    $this->assertNotNull($rejections);
    $this->assertCount(1, $rejections);

    $reject = $rejections[0];

    $this->assertArraySubset([
      'resource' => $resource,
      'action' => '*'
    ], $reject);

  }

  public function testAllowDeny() {

    $listener = new BaseAccessControlAPIListener($this->app);

    $userId = 1;
    $userStub = $this->getUserStub($userId);

    $resource = new Resource('user', $userId, 'password');

    $context = new Context();
    $context->expose('user', $userStub);
    $context->expose('resource', $resource);
    $context->expose('_authorizations', new PermissionCollection());
    $context->expose('_rejections', new PermissionCollection());

    $rules = [
      new Rule('owns(resource)', ["allow('*', resource)"]),
      new Rule('owns(resource) and resource.type == "user" and resource.property == "password"', ['deny("*", resource)'])
    ];

    $this->ruleEngine->execute(RuleEngine::ACCESS_CONTROL, $rules, $context);

    $rejections = $context->getVariable('_rejections')->getSource();

    $this->assertNotNull($rejections);
    $this->assertCount(1, $rejections);

    $reject = $rejections[0];

    $this->assertArraySubset([
      'resource' => $resource,
      'action' => '*'
    ], $reject);

  }

  protected function getUserStub($userId) {
    $userStub = $this->getMockBuilder(User::class)
      ->setMethods(['getId'])
      ->getMock()
    ;
    $userStub->method('getId')->willReturn($userId);
    return $userStub;
  }

}
