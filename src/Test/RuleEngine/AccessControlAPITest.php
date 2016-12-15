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
    $listener = new BaseAccessControlAPIListener($this->app);
  }

  public function testOwnsAllowMethod() {

    $userStub = $this->getUserStub(1);
    $resource = new Resource('user', 1);
    $context = $this->getAccessControlContext($userStub, $resource);

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

    $userId = 1;
    $userStub = $this->getUserStub($userId);

    $notUserId = 2;
    $resource = new Resource('user', $notUserId, 'password');
    $context = $this->getAccessControlContext($userStub, $resource);

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

    $userId = 1;
    $userStub = $this->getUserStub($userId);
    $resource = new Resource('user', $userId, 'password');
    $context = $this->getAccessControlContext($userStub, $resource);
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


  public function testMatch() {

    $user = $this->getUserStub(1);
    $resource = new Resource('foo', 1);
    $context = $this->getAccessControlContext($user, $resource);

    $rules = [
      new Rule('match(resource, "foo")', ["allow('*', resource)"])
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


  protected function getAccessControlContext($user, $resource) {

    $context = new Context();

    $context->expose('user', $user);
    $context->expose('resource', $resource);
    $context->expose('_authorizations', new PermissionCollection());
    $context->expose('_rejections', new PermissionCollection());

    return $context;

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
