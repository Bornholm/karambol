<?php

namespace Karambol\RuleEngine\Context;

use Karambol\RuleEngine\Context\Variable;

class Context {

  protected $variables = [];

  public function expose($name, $value) {
    $var = new Variable($value);
    $this->variables[$name] = $var;
    return $var;
  }

  public function getVariable($name) {
    $variables = $this->variables;
    return isset($variables[$name]) ? $variables[$name] : null;
  }

  public function toArray() {
    $vars = [];
    foreach($this->variables as $name => $variable) {
      $vars[$name] = $variable;
    }
    return $vars;
  }

}
