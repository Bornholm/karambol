<?php

namespace Karambol\Menu;

class MenuService {

  protected $menus = [];

  public function __construct() {
    $this->menus = [];
  }

  public function getMenu($menuName) {
    return isset($this->menus[$menuName]) ?
      $this->menus[$menuName] :
      ( $this->menus[$menuName] = new Menu($menuName) )
    ;
  }

  public function setMenu(Menu $menu) {
    $this->menus[$menuName] = $menu;
    return $this;
  }

}
