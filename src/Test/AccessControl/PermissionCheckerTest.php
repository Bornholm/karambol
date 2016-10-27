<?php

namespace Karambol\Test\AccessControl;

use Karambol\AccessControl\Permission\PermissionCollection;
use Karambol\AccessControl\Permission\PermissionChecker;
use Karambol\AccessControl\BaseActions;
use Karambol\AccessControl\Resource;

class PermissionCheckerTest extends \PHPUnit_Framework_TestCase
{

  public function testWithoutAuthorizations() {

    $action = BaseActions::ACCESS;
    $resource = new Resource('page', 'home');

    $authorizations = new PermissionCollection();
    $permCheck = new PermissionChecker($authorizations);

    $isAllowed = $permCheck->isAllowed($action, $resource);

    $this->assertFalse($isAllowed, 'Without authorization, the action should be forbidden !');

  }

  public function testWithAuthorizations() {

    $action = BaseActions::ACCESS;
    $resource = new Resource('page', 'home');

    $authorizations = new PermissionCollection();
    $authorizations->add($action, $resource);

    $permCheck = new PermissionChecker($authorizations);

    $isAllowed = $permCheck->isAllowed($action, $resource);

    $this->assertTrue($isAllowed);

  }

  public function testRejectionsPriority() {

    $action = BaseActions::ACCESS;
    $resource = new Resource('page', 'home');

    $authorizations = new PermissionCollection();
    $authorizations->add($action, $resource);

    $rejections = new PermissionCollection();
    $rejections->add($action, $resource);

    $permCheck = new PermissionChecker($authorizations, $rejections);

    $isAllowed = $permCheck->isAllowed($action, $resource);

    $this->assertFalse($isAllowed, 'Rejections should have priority over authorizations !');

  }

  public function testWildcardAuthorization() {

    $action = BaseActions::CREATE;
    $resource = new Resource('post', '1');

    $authorizations = new PermissionCollection();
    $authorizations->add('*', '*');

    $permCheck = new PermissionChecker($authorizations);

    $isAllowed = $permCheck->isAllowed($action, $resource);

    $this->assertTrue($isAllowed);

  }

  public function testWildcardResource() {

    $action = BaseActions::CREATE;
    $resource = new Resource('post', '1');

    $authorizations = new PermissionCollection();
    $authorizations->add('*', '*');

    $permCheck = new PermissionChecker($authorizations);

    $isAllowed = $permCheck->isAllowed($action, $resource);

    $this->assertTrue($isAllowed);

  }



}
