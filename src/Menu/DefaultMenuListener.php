<?php

namespace Karambol\Menu;

use Karambol\KarambolApp;
use Karambol\Menu\MenuEvent;
use Karambol\Menu\MenuItem;
use Karambol\Menu\Menu;
use Karambol\Menu\Menus;
use Karambol\Menu\MenuItems;
use Karambol\Entity\RuleSet;

class DefaultMenuListener {

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
    }

  }

  protected function configureAdminMainMenu(Menu $menu) {

    $urlGen = $this->app['url_generator'];

    $usersItem = new MenuItem(MenuItems::ADMIN_USERS, $urlGen->generate('admin_users'), [
      'icon_class' => 'fa fa-users'
    ]);
    $menu->addItem($usersItem);

    $linksItem = new MenuItem(MenuItems::ADMIN_PAGES, $urlGen->generate('admin_pages'), [
      'icon_class' => 'fa fa-link'
    ]);
    $menu->addItem($linksItem);

    $rulesItem = new MenuItem(MenuItems::ADMIN_RULES, '', [
      'icon_class' => 'fa fa-gavel'
    ]);
    $rulesItem
      ->addItem(new MenuItem(
        MenuItems::ADMIN_RULES_CUSTOMIZATION,
        $urlGen->generate(sprintf('admin_rules_%s', RuleSet::CUSTOMIZATION)),
        ['icon_class' => 'fa fa-rocket']
      ))
      ->addItem(new MenuItem(MenuItems::ADMIN_RULES_ROLES, '', ['icon_class' => 'fa fa-shield']))
    ;
    $menu->addItem($rulesItem);

    $configItem = new MenuItem(MenuItems::ADMIN_CONFIGURATION, '', [
      'icon_class' => 'fa fa-cog'
    ]);
    $menu->addItem($configItem);

    $pluginsItem = new MenuItem(MenuItems::ADMIN_PLUGINS, '', [
      'icon_class' => 'fa fa-cubes'
    ]);
    $pluginsItem->addItem(new MenuItem('Foo Plugin', ''));
    $menu->addItem($pluginsItem);

    $homeItem = new MenuItem(MenuItems::HOME, $urlGen->generate('home'), [
      'align' => 'right',
      'icon_class' => 'fa fa-home'
    ]);
    $menu->addItem($homeItem);

    $logoutItem = new MenuItem(MenuItems::LOGOUT, $urlGen->generate('logout'), [
      'align' => 'right',
      'icon_class' => 'fa fa-sign-out'
    ]);
    $menu->addItem($logoutItem);

  }

}
