<?php

namespace Karambol\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Karambol\Menu\MenuService;
use Karambol\Menu\MenuEvent;

class MenuServiceProvider implements ServiceProviderInterface
{

    public function register(Application $app) {

      $menu = new MenuService();
      $app['menu'] = $menu;

      $app['twig'] = $app->share($app->extend('twig', function($twig, $app) {

        $twig->addFunction(new \Twig_SimpleFunction('menu', function ($menuName) use ($app) {
          $menu = $app['menu']->getMenu($menuName);
          $eventName = MenuService::getMenuEvent($menuName);
          $event = new MenuEvent($menuName, $menu);
          $app['menu']->dispatch($eventName, $event);
          return $app['twig']->render('menus/'.$menuName.'.html.twig', [
            'items' => $menu->getItems()
          ]);
        }, ['is_safe' => ['html', 'js']]));

        return $twig;

      }));

    }

    public function boot(Application $app) {}

}