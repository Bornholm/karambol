<?php

namespace Karambol\Setting;

use Karambol\KarambolApp;
use Karambol\Setting\SettingEntry;
use Karambol\VirtualSet\ItemCountEvent;
use Karambol\VirtualSet\ItemSearchEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BaseSettingsSubscriber implements EventSubscriberInterface {

  protected $app;

  public function __construct(KarambolApp $app) {
    $this->app = $app;
  }

  public static function getSubscribedEvents() {
    return [
      ItemSearchEvent::NAME => 'onSearchSettings',
      ItemCountEvent::NAME => 'onCountSettings'
    ];
  }

  public function onSearchSettings(ItemSearchEvent $event) {
    $entries = $this->getBaseSettings();
    $event->addItems($entries);
  }

  public function onCountSettings(ItemCountEvent $event) {
    $entries = $this->getBaseSettings();
    $event->add(count($entries));
  }

  protected function getBaseSettings() {

    $entries = [];

    $entries[] = new SettingEntry('portal_title', 'MSE', 'admin.settings.portal_title_help');

    $themes = $this->app['themes'];
    $defaultThemeEntry = new SettingEntry('default_theme', $themes->getSelectedTheme(), 'admin.settings.default_theme_help');
    $themeChoices = [];
    foreach($themes->getAvailableThemes() as $theme) {
      $themeChoices[$theme] = $theme;
    }
    $defaultThemeEntry->setChoices($themeChoices);

    $entries[] = $defaultThemeEntry;

    return $entries;
  }

}
