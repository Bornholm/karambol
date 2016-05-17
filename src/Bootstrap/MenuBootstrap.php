<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Provider;
use Karambol\Menu;

class MenuBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {

    // Register menu service
    $app->register(new Provider\MenuServiceProvider());

    // Init default menu listeners
    $menuListener = new Menu\MenuListener($app);

    $app['menu']->addListener(
      Menu\MenuEvent::MENU_RENDER,
      [$menuListener, 'onMenuRender']
    );

  }

}