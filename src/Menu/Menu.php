<?php

namespace Karambol\Menu;

class Menu {

  protected $items;

  public function __construct() {
    $this->items = [];
  }

  public function addItem(MenuItem $menuItem) {
    $this->items[] = $menuItem;
    return $this;
  }

  public function removeItem(MenuItem $item) {
    if(!in_array($item)) return $this;
    array_splice($this->items, array_search($item, $this->items));
    return $this;
  }

  public function getItemByName($itemName) {
    foreach($items as $item) {
      if($item->getName() === $itemName) return $item;
      $subResult = $item->getItemByName($itemName);
      if($subResult) return $subResult;
    }
    return null;
  }

  public function getItems() {
    return $this->items;
  }

}
