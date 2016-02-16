<?php

namespace Karambol\Service;

class MenuService
{

  protected $menus;

  public function __construct() {
    $this->menus = [];
  }

  public function addItem($menuName, $menuItem) {
    $menus = $this->menus;
    $menu = isset($menus[$menuName]) ? $menus[$menuName] : ($menus[$menuName] = []);
    $menu[] = $menuItem;
    $this->menus[$menuName] = $menu;
    return $this;
  }

  public function removeItem($menuName, $itemIndex) {
    $menus = $this->menus;
    $menu = isset($menus[$menuName]) ? $menus[$menuName] : ($menus[$menuName] = []);
    if($menu) {
      array_splice($menu, $itemIndex);
      $this->menus[$menuName] = $menu;
    }
    return $this;
  }

  public function getItems($menuName) {
    $menus = $this->menus;
    $menu = isset($menus[$menuName]) ? $menus[$menuName] : ($menus[$menuName] = []);
    return $menu;
  }

}
