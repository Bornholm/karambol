<?php

namespace Karambol\Menu;

use Karambol\KarambolApp;
use Karambol\Menu\MenuEvent;
use Karambol\Menu\MenuItem;
use Karambol\Menu\Menu;
use Karambol\Menu\Menus;
use Karambol\Menu\MenuItems;

class MenuListener {

  protected $app;

  public function __construct(KarambolApp $app) {
    $this->app = $app;
  }

  public function onMenuRender(MenuEvent $event) {

    $menu = $event->getMenu();

    switch($event->getMenuName()) {
      case Menus::ADMIN_MAIN:
        $this->configureAdminMainMenu($menu);
        break;
      case Menus::HOME_MAIN:
        $this->configureHomepageMainMenu($menu);
    }

  }

  protected function configureAdminMainMenu(Menu $menu) {

    $urlGen = $this->app['url_generator'];

    $usersItem = new MenuItem(MenuItems::ADMIN_USERS, $urlGen->generate('admin_users'), [
      'icon_class' => 'fa fa-users'
    ]);
    $menu->addItem($usersItem);

    $linksItem = new MenuItem(MenuItems::ADMIN_PAGES, '', [
      'icon_class' => 'fa fa-link'
    ]);
    $menu->addItem($linksItem);

    $rulesItem = new MenuItem(MenuItems::ADMIN_RULES, '', [
      'icon_class' => 'fa fa-gavel'
    ]);
    $rulesItem->addItem(new MenuItem(MenuItems::ADMIN_RULES_MENUS, '', ['icon_class' => 'fa fa-bars']))
      ->addItem(new MenuItem(MenuItems::ADMIN_RULES_ROLES, '', ['icon_class' => 'fa fa-shield']))
    ;
    $menu->addItem($rulesItem);

    $pluginsItem = new MenuItem(MenuItems::ADMIN_PLUGINS, '', [
      'icon_class' => 'fa fa-cubes'
    ]);
    $pluginsItem->addItem(new MenuItem('Foo Plugin'));
    $menu->addItem($pluginsItem);

    $homeItem = new MenuItem(MenuItems::HOME, $urlGen->generate('home'), [
      'align' => 'right',
      'icon_class' => 'fa fa-home'
    ]);
    $menu->addItem($homeItem);

    $configItem = new MenuItem(MenuItems::ADMIN_CONFIGURATION, '', [
      'align' => 'right',
      'icon_class' => 'fa fa-cog'
    ]);
    $menu->addItem($configItem);

    $logoutItem = new MenuItem(MenuItems::LOGOUT, $urlGen->generate('logout'), [
      'align' => 'right',
      'icon_class' => 'fa fa-sign-out'
    ]);
    $menu->addItem($logoutItem);

  }

  protected function configureHomepageMainMenu(Menu $menu) {

    $urlGen = $this->app['url_generator'];

    $adminItem = new MenuItem(MenuItems::ADMIN, $urlGen->generate('admin'), [
      'align' => 'right',
      'icon_class' => 'fa fa-flash'
    ]);
    $menu->addItem($adminItem);

    $loginItem = new MenuItem(MenuItems::LOGIN, $urlGen->generate('login'), [
      'align' => 'right',
      'icon_class' => 'fa fa-sign-in'
    ]);
    $menu->addItem($loginItem);

  }

}
