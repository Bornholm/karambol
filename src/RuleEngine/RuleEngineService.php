<?php

namespace Karambol\RuleEngine;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Doctrine\ORM\EntityManager;

class RuleEngineService extends EventDispatcher {

  protected $em;
  protected $api;

  public function __construct(EntityManager $em) {
    $this->em = $em;
    $api = [];
  }

  public function execute(array $rules, array $vars = null) {

    $language = new ExpressionLanguage();

    $context = array_merge($this->api, is_array($vars) ? $vars: []);

    foreach($rules as $r) {
      $result = $language->evaluate($r->getCondition(), $context);
      if($result) $language->evaluate($r->getAction(), $context);
    }

  }

  public function registerEngineMethod($methodeName, callable $callback) {
    $this->api[$methodeName] = $callback;
  }

  public function unergisterEngineMethod($methodeName) {
    unset($this->api[$methodeName]);
  }

  public function getEngineMethod($methodeName) {
    return $this->isEngineMethodeRegistered($methodName) ? $this->api[$methodeName] : null;
  }

  public function isEngineMethodRegistered($methodName) {
    return isset($this->api[$methodeName]);
  }

}
