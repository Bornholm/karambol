<?php

namespace Karambol\RuleEngine;

class RuleEngineAPIFactory {

  protected $methods = [];

  public function registerMethod($methodName, callable $callback) {
    $this->methods[$methodName] = $callback;
  }

  public function unregisterMethod($methodName) {
    unset($this->methods[$methodName]);
  }

  public function hasMethod($methodName) {
    return isset($this->methods[$methodName]);
  }

  public function getAPI() {
    return new RuleEngineAPI($this->methods);
  }

}

class RuleEngineAPI {

  protected $methods;

  public function __construct($methods) {
    $this->methods = $methods;
  }

  public function __call($method, $args) {
    if(!isset($this->methods[$method])) {
      throw new \Exception(sprintf('The RuleEngine API method "%s()" does not exist !', $method));
    }
    return call_user_func_array($this->methods[$method], $args);
  }

}
