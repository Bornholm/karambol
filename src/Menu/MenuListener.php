<?php

namespace Karambol\Menu;

use Karambol\KarambolApp;
use Karambol\Menu\MenuEvent;

class MenuListener {

  public function onMenuRender(MenuEvent $event) {

    $menu = $event->getMenu();

    $menu->addItem([
      'label' => 'admin.navbar.users',
      'route' => 'admin_users'
    ]);

  }

}
