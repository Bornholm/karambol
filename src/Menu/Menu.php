<?php

namespace Karambol\Menu;
use Karambol\VirtualSet\VirtualSet;

class Menu extends VirtualSet {

  protected $name;

  public function __construct($name) {
    $this->name = $name;
  }

  public function getName() {
    return $this->name;
  }

}
