<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Provider;
use Karambol\Menu;
use Karambol\Listener;

class MenuBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {

    // Register menu service
    $app->register(new Provider\MenuServiceProvider());

    // Init default menu listeners
    $adminMenuListener = new Listener\AdminMenuListener();

    $app['menu']->addListener(
      Menu\MenuService::getMenuEvent('admin_main'),
      [$adminMenuListener, 'onMainMenuRender']
    );

  }

}
