<?php

namespace Karambol\Menu;

use Symfony\Component\EventDispatcher\EventDispatcher;

class MenuService extends EventDispatcher {

  protected $menus = [];

  public function __construct() {
    $this->menus = [];
  }

  public function getMenu($menuName) {
    $menus = $this->menus;
    $menu = isset($menus[$menuName]) ? $menus[$menuName] : ($menus[$menuName] = new Menu());
    return $menu;
  }

  public function setMenu(Menu $menu) {
    $this->menus[$menuName] = $menu;
    return $this;
  }

}
