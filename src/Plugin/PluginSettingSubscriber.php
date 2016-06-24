<?php

namespace Karambol\Plugin;

use Karambol\KarambolApp;
use Karambol\Setting\SettingEntry;
use Karambol\VirtualSet\ItemCountEvent;
use Karambol\VirtualSet\ItemSearchEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PluginSettingSubscriber implements EventSubscriberInterface {

  protected $pluginId;

  public function __construct($pluginId) {
    $this->pluginId = $pluginId;
  }

  public static function getSubscribedEvents() {
    return [
      ItemSearchEvent::NAME => 'onSearchSetting',
      ItemCountEvent::NAME => 'onCountSetting'
    ];
  }

  public function onSearchSetting(ItemSearchEvent $event) {

    $settingName = $this->getPluginSettingName($this->pluginId);
    $settingDescKey = sprintf('admin.settings.%s_help', $settingName);
    $settingEntry = new SettingEntry($settingName, false, $settingDescKey);

    $event->addItem($settingEntry);

  }

  public function onCountSetting(ItemCountEvent $event) {
    $event->add(1);
  }

  protected function getPluginSettingName($pluginId) {
    return sprintf('enable_plugin_%s', $pluginId);
  }

}
