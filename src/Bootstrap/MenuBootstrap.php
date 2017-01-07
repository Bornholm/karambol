<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Provider;
use Karambol\Menu;

/**
 * Initialisation menu
 * @package Karambol
 * @license AGPLv3
 * @author William Petit
 */
class MenuBootstrap implements BootstrapInterface {
  
  /**
   * Initialisation
   * @param KarambolApp $app
   * @author William Petit
   */
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
