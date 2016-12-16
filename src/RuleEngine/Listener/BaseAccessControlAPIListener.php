<?php

namespace Karambol\RuleEngine\Listener;

use Karambol\RuleEngine\RuleEngineEvent;
use Karambol\RuleEngine\RuleEngine;
use Karambol\KarambolApp;
use Karambol\Menu\MenuItem;
use Karambol\Page\PageInterface;
use Karambol\Page\Page;
use Karambol\Entity\User;
use Karambol\AccessControl\Parser\ResourceSelectorParser;
use Karambol\AccessControl\ResourceSelector;
use Karambol\AccessControl\ResourceOwnerInterface;
use Karambol\AccessControl\ResourceInterface;
use Karambol\RuleEngine\Variable\VariableValue;
use Karambol\RuleEngine\Context\ProtectableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class BaseAccessControlAPIListener {

  use \Karambol\RuleEngine\Listener\CommonAPITrait;

  public function onBeforeExecuteRules(RuleEngineEvent $event) {

    if($event->getType() !== RuleEngine::ACCESS_CONTROL) return;

    $app = $this->app;
    $provider = $event->getFunctionProvider();

    $this->registerCommonAPI($provider);
    $provider->registerFunction('owns', [$this, 'owns']);
    $provider->registerFunction('allow', [$this, 'allow']);
    $provider->registerFunction('deny', [$this, 'deny']);
    $provider->registerFunction('addRole', [$this, 'addRole']);
    $provider->registerFunction('removeRole', [$this, 'removeRole']);
    $provider->registerFunction('hasRole', [$this, 'hasRole']);
    $provider->registerFunction('match', [$this, 'match']);

  }

  public function owns(array $vars, $resource) {

    $app = $this->app;

    /* @var Karambol/AccessControl/ResourceInterface */
    $resource = $this->unwrap($resource);
    $user = $this->unwrap($vars['user']);

    if(!($user instanceof ResourceOwnerInterface)) {
      throw new \Exception('The context user must implement the ResourceOwnerInterface !');
    }

    $resource = $this->unwrap($resource);

    if(!($resource instanceof ResourceInterface)) {
      throw new \Exception('The resource argument must implement the ResourceInterface !');
    }

    // If the resource has not identifier, consider that it has no specific owner yet
    if(empty($resource->getResourceId())) return true;

    return $user->owns($resource);

  }

  public function allow(array $vars, $action, $resource) {
    $authorizations = $this->unwrap($vars['_authorizations']);
    $action = $this->unwrap($action);
    $resource = $this->unwrap($resource);
    $authorizations->add($action, $resource);
  }

  public function deny(array $vars, $action, $resource) {
    $rejections = $this->unwrap($vars['_rejections']);
    $action = $this->unwrap($action);
    $resource = $this->unwrap($resource);
    $rejections->add($action, $resource);
  }

  public function addRole(array $vars, $role) {
    $user = $this->unwrap($vars['user']);
    $role = $this->unwrap($role);
    if($user instanceof UserInterface) $user->addRole($role);
  }

  public function removeRole(array $vars, $role) {
    $user = $this->unwrap($vars['user']);
    $role = $this->unwrap($role);
    if($user instanceof UserInterface) $user->removeRole($role);
  }

  public function hasRole(array $vars, $role) {
    $user = $this->unwrap($vars['user']);
    $role = $this->unwrap($role);
    if($user instanceof UserInterface) return in_array($role, $user->getRoles());
    return false;
  }

  public function match(array $vars, $resource, $criteria) {

    $resource = $this->unwrap($resource);
    $criteria = $this->unwrap($criteria);

    if($criteria instanceof ResourceInterface) {
      return $resource === $selectorOrResource;
    }

    if(is_string($criteria)) {
      $parser = new ResourceSelectorParser();
      $selector = $parser->parse($criteria);
      return $selector->matches($resource);
    }

    throw new \InvalidArgumentException('The $criteria argument must be a valid resource selector or implements ResourceInterface !');

  }

}
