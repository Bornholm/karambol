<?php

namespace Karambol\Menu;

use Karambol\KarambolApp;
use Karambol\Menu\MenuEvent;
use Karambol\Menu\MenuItem;
use Karambol\Menu\Menu;
use Karambol\Menu\Menus;
use Karambol\Menu\MenuItems;
use Karambol\RuleEngine\RuleEngineService;

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

    $contentItem = new MenuItem(MenuItems::ADMIN_CONTENT, '#', [
      'icon_class' => 'fa fa-puzzle-piece'
    ]);
    $menu->addItem($contentItem);

    $linksItem = new MenuItem(MenuItems::ADMIN_PAGES, $urlGen->generate('admin_pages'), [
      'icon_class' => 'fa fa-link'
    ]);
    $contentItem->addItem($linksItem);

    $rulesItem = new MenuItem(MenuItems::ADMIN_RULES, '', [
      'icon_class' => 'fa fa-gavel'
    ]);
    $rulesItem
      ->addItem(new MenuItem(
        MenuItems::ADMIN_RULES_CUSTOMIZATION,
        $urlGen->generate(sprintf('admin_rules_%s', RuleEngineService::CUSTOMIZATION)),
        ['icon_class' => 'fa fa-rocket']
      ))
      ->addItem(new MenuItem(
        MenuItems::ADMIN_RULES_ACCES_CONTROL,
        $urlGen->generate(sprintf('admin_rules_%s', RuleEngineService::ACCESS_CONTROL)),
        ['icon_class' => 'fa fa-shield']
      ))
    ;
    $menu->addItem($rulesItem);

    $configItem = new MenuItem(MenuItems::ADMIN_CONFIGURATION, '', [
      'icon_class' => 'fa fa-cog'
    ]);
    $menu->addItem($configItem);

    $pluginsItem = new MenuItem(MenuItems::ADMIN_PLUGINS, '', [
      'icon_class' => 'fa fa-plug'
    ]);
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
