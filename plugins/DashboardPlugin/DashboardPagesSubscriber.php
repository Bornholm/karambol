<?php

namespace DashboardPlugin;

use Karambol\KarambolApp;
use Karambol\Setting\SettingEntry;
use Karambol\VirtualSet\ItemCountEvent;
use Karambol\VirtualSet\ItemSearchEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Karambol\Util\AppAwareTrait;

class DashboardPagesSubscriber implements EventSubscriberInterface {

  public static function getSubscribedEvents() {
    return [
      ItemSearchEvent::NAME => 'onSearchPages',
      ItemCountEvent::NAME => 'onCountPages'
    ];
  }

  public function onSearchPages(ItemSearchEvent $event) {
    $page = new Page('pages.plugins.dashboard', $urlGen->generate('home'), 'home');

  }

  public function onCountPages(ItemCountEvent $event) {

  }

}
