<?php

namespace Karambol\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Karambol\Service\MenuService;

class MenuServiceProvider implements ServiceProviderInterface
{

    public function register(Application $app) {

      $menu = new MenuService();
      $app['menu'] = $menu;

      $app['twig'] = $app->share($app->extend('twig', function($twig, $app) {

        $twig->addFunction(new \Twig_SimpleFunction('menu', function ($menuName) use ($app) {
          return $app['twig']->render('menus/'.$menuName.'.html.twig', [
            'items' => $app['menu']->getItems($menuName)
          ]);
        }, ['is_safe' => ['html']]));

        return $twig;

      }));

    }

    public function boot(Application $app) {}

}
