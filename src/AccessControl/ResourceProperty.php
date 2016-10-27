<?php

namespace Karambol\AccessControl;

use Karambol\AccessControl\ResourceInterface;

class ResourceProperty {

  protected $name;
  protected $parent;

  public function __construct(ResourceInterface $parent, $name) {
    $this->parent = $parent;
    $this->name = $name;
  }

  public function getName() {
    return $this->name;
  }

  public function getParent() {
    return $this->parent;
  }

}
