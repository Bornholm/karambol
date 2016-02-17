<?php

namespace Karambol\Menu;

class Menu {

  protected $items;

  public function __construct() {
    $this->items = [];
  }

  public function addItem($menuItem) {
    $this->items[] = $menuItem;
    return $this;
  }

  public function removeItem($itemIndex) {
    array_splice($this->items, $itemIndex);
    return $this;
  }

  public function getItems() {
    return $this->items;
  }

}
