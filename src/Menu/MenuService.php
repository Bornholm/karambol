<?php

namespace Karambol\Menu;

use Symfony\Component\EventDispatcher\EventDispatcher;

class MenuService extends EventDispatcher {

  protected $menus = [];

  public function __construct() {
    $this->menus = [];
  }

  public function getMenu($menuName) {
    return isset($this->menus[$menuName]) ?
      $this->menus[$menuName] :
      ( $this->menus[$menuName] = new Menu() )
    ;
  }

  public function setMenu(Menu $menu) {
    $this->menus[$menuName] = $menu;
    return $this;
  }

}
