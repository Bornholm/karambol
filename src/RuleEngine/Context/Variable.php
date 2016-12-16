<?php

namespace Karambol\RuleEngine\Context;
use Karambol\RuleEngine\Context\ProtectableInterface;

class Variable {

  protected $source;

  public function __construct($source) {
    $this->source = $source;
  }

  public function getSource() {
    return $this->source;
  }

  public function isProtected() {
    return $this->source instanceof ProtectableInterface;
  }

  public function __get($name) {
    $source = $this->getSource();
    if($this->isProtected()) $source = $source->getExposedAttributes();
    return is_array($source) ? $source[$name] : $source->$name;
  }

  public function __toString() {
    if(method_exists($this->getSource(), '__toString')) {
      return $this->getSource()->__toString();
    }
    throw new \Exception('This variable has no string representation !');
  }

}
