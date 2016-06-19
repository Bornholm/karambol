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
    $mainAdminMenuSubscriber = new Menu\AdminMainMenuSubscriber($app);
    $mainAdminMenu = $app['menus']->getMenu(Menu\Menus::ADMIN_MAIN);
    $mainAdminMenu->addSubscriber($mainAdminMenuSubscriber);

    $app['twig'] = $app->share($app->extend('twig', function($twig, $app) {

      $twig->addFunction(new \Twig_SimpleFunction('menu', function ($menuName) use ($app) {
        $menu = $app['menus']->getMenu($menuName);
        return $app['twig']->render('menus/'.$menuName.'.html.twig', [ 'menu' => $menu ]);
      }, ['is_safe' => ['html', 'js']]));

      return $twig;

    }));

  }

}
