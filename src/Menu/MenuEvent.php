<?php

namespace Karambol\Menu;

use Symfony\Component\EventDispatcher\Event;

class MenuEvent extends Event {

  const MENU_RENDER = 'menu.render';

  protected $menu;
  protected $menuName;

  public function __construct($menuName, &$menu) {
    $this->menu = $menu;
    $this->menuName = $menuName;
  }

  public function getMenu() {
    return $this->menu;
  }

  public function getMenuName() {
    return $this->menuName;
  }

}
