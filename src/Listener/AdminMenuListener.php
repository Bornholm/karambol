<?php

namespace Karambol\Listener;

use Karambol\KarambolApp;
use Karambol\Menu\MenuEvent;

class AdminMenuListener {

  public function onMainMenuRender(MenuEvent $event) {

    $menu = $event->getMenu();

    $menu->addItem([
      'label' => 'admin.navbar.users',
      'route' => 'admin_users'
    ]);

  }

}
