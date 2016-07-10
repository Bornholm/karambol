<?php

namespace Karambol\Setting;

use Karambol\KarambolApp;
use Karambol\Setting\SettingEntry;
use Karambol\VirtualSet\ItemCountEvent;
use Karambol\VirtualSet\ItemSearchEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Karambol\Util\AppAwareTrait;

class BaseSettingsSubscriber implements EventSubscriberInterface {

  use AppAwareTrait;

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

    // Ajout paramètre titre du portail
    $portalTitleEntry = new SettingEntry('portal_title', 'Karambol');
    $portalTitleEntry
      ->setLabel('admin.settings.portal_title')
      ->setHelp('admin.settings.portal_title_help')
    ;
    $entries[] = $portalTitleEntry;

    // Ajout du paramètre de thème par défaut
    $themes = $this->app['themes'];
    $defaultThemeEntry = new SettingEntry('default_theme', $themes->getDefaultTheme());
    $defaultThemeEntry
      ->setLabel('admin.settings.default_theme')
      ->setHelp('admin.settings.default_theme_help')
    ;
    $themeChoices = [];
    foreach($themes->getAvailableThemes() as $theme) {
      $themeChoices[$theme] = $theme;
    }
    $defaultThemeEntry->setChoices($themeChoices);

    $entries[] = $defaultThemeEntry;

    return $entries;
  }

}
