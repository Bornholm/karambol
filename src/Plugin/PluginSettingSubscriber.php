<?php

namespace Karambol\Plugin;

use Karambol\KarambolApp;
use Karambol\Setting\SettingEntry;
use Karambol\VirtualSet\ItemCountEvent;
use Karambol\VirtualSet\ItemSearchEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PluginSettingSubscriber implements EventSubscriberInterface {

  protected $app;
  protected $pluginName;

  public function __construct(KarambolApp $app, $pluginName) {
    $this->app = $app;
    $this->pluginName = $pluginName;
  }

  public static function getSubscribedEvents() {
    return [
      ItemSearchEvent::NAME => 'onSearchSetting',
      ItemCountEvent::NAME => 'onCountSetting'
    ];
  }

  public function onSearchSetting(ItemSearchEvent $event) {

    $settingName = $this->getPluginSettingName($this->pluginName);
    $settingEntry = new SettingEntry($settingName, false);

    $trans = $this->app['translator'];
    $settingLabel = $trans->trans('admin.settings.enable_plugin', [
      '%plugin_name%' => $this->pluginName
    ]);
    $settingEntry->setLabel($settingLabel);

    $event->addItem($settingEntry);

  }

  public function onCountSetting(ItemCountEvent $event) {
    $event->add(1);
  }

  protected function getPluginSettingName($pluginName) {
    return sprintf('enable_plugin_%s', $pluginName);
  }

}
