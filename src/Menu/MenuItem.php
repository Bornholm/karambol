<?php

namespace Karambol\Menu;

class MenuItem {

  protected $name;
  protected $link;
  protected $items;
  protected $attrs;

  public function __construct($name, $link, array $attrs = []) {
    $this->name = $name;
    $this->link = $link;
    $this->attrs = $attrs;
    $this->items = [];
  }

  public function getLink() {
    return $this->link;
  }

  public function getName() {
    return $this->name;
  }

  public function getItemByName($itemName) {
    foreach($items as $item) {
      if($item->getName() === $itemName) return $item;
      $subResult = $item->getSubItemByName($itemName);
      if($subResult) return $subResult;
    }
    return null;
  }

  public function addItem(MenuItem $item) {
    $this->items[] = $item;
    return $this;
  }

  public function removeItem(MenuItem $item) {
    if(!in_array($item)) return $this;
    array_splice($this->items, array_search($item, $this->items));
    return $this;
  }

  public function getItems() {
    return $this->items;
  }

  public function hasItems() {
    return count($this->items) > 0;
  }

  public function setAttribute($attrName, $attrValue) {
    $this->attrs[$attrName] = $attrValue;
  }

  public function getAttribute($attrName) {
    return isset($this->attrs[$attrName]) ? $this->attrs[$attrName] : null;
  }

  public function getAttributes() {
    return $this->attrs;
  }

}
