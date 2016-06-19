<?php

namespace Karambol\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Karambol\Menu\MenuService;

class MenuServiceProvider implements ServiceProviderInterface
{

    public function register(Application $app) {
      $menu = new MenuService();
      $app['menus'] = $menu;
    }

    public function boot(Application $app) {}

}
