<?php

namespace Karambol\Menu;

use Karambol\KarambolApp;
use Karambol\Menu\MenuItem;
use Karambol\Menu\Menu;
use Karambol\Menu\MenuItems;
use Karambol\RuleEngine\RuleEngineService;
use Karambol\VirtualSet\ItemSearchEvent;
use Karambol\VirtualSet\ItemCountEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AdminMainMenuSubscriber implements EventSubscriberInterface {

  protected $app;

  public function __construct(KarambolApp $app) {
    $this->app = $app;
  }

  public static function getSubscribedEvents() {
    return [
      ItemSearchEvent::NAME => 'onMenuItemsSearch',
      ItemCountEvent::NAME => 'onMenuItemsCount'
    ];
  }

  public function onMenuItemsCount(ItemCountEvent $event) {
    $menuItems = $this->getBaseItems();
    $event->add(count($menuItems));
  }

  public function onMenuItemsSearch(ItemSearchEvent $event) {
    $menuItems = $this->getBaseItems();
    $event->addItems($menuItems);
  }

  protected function getBaseItems() {

    $items = [];
    $urlGen = $this->app['url_generator'];

    $usersItem = new MenuItem(MenuItems::ADMIN_USERS, $urlGen->generate('admin_users_list'), [
      'icon_class' => 'fa fa-users'
    ]);
    $items[] = $usersItem;

    $pagesItem = new MenuItem(MenuItems::ADMIN_PAGES, $urlGen->generate('admin_pages_list'), [
      'icon_class' => 'fa fa-link'
    ]);
    $items[] = $pagesItem;

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
    $items[] = $rulesItem;

    $configItem = new MenuItem(
      MenuItems::ADMIN_CONFIGURATION,
      $urlGen->generate('settings'),
      ['icon_class' => 'fa fa-cog']
    );
    $items[] = $configItem;

    $pluginsItem = new MenuItem(MenuItems::ADMIN_PLUGINS, '', [
      'icon_class' => 'fa fa-plug'
    ]);
    $items[] = $pluginsItem;

    $homeItem = new MenuItem(MenuItems::HOME, $urlGen->generate('home'), [
      'align' => 'right',
      'icon_class' => 'fa fa-home'
    ]);
    $items[] = $homeItem;

    $logoutItem = new MenuItem(MenuItems::LOGOUT, $urlGen->generate('logout'), [
      'align' => 'right',
      'icon_class' => 'fa fa-sign-out'
    ]);
    $items[] = $logoutItem;

    return $items;

  }

}
