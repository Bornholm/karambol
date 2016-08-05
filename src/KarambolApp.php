<?php

namespace Karambol;

use Silex\Application;

class KarambolApp extends Application
{

  public function __construct() {
    parent::__construct();
    $this->bootstrapApplication();
  }

  protected function bootstrapApplication() {

    $bootrappers = [
      'Karambol\Bootstrap\ConfigBootstrap',
      'Karambol\Bootstrap\AppPathBootstrap',
      'Karambol\Bootstrap\DoctrineBootstrap',
      'Karambol\Bootstrap\SettingBootstrap',
      'Karambol\Bootstrap\AccountBootstrap',
      'Karambol\Bootstrap\MonologBootstrap',
      'Karambol\Bootstrap\RuleEngineBootstrap',
      'Karambol\Bootstrap\SessionBootstrap',
      'Karambol\Bootstrap\FormBootstrap',
      'Karambol\Bootstrap\UrlGeneratorBootstrap',
      'Karambol\Bootstrap\TwigBootstrap',
      'Karambol\Bootstrap\AssetBootstrap',
      'Karambol\Bootstrap\SlugifyBootstrap',
      'Karambol\Bootstrap\PageBootstrap',
      'Karambol\Bootstrap\ThemeBootstrap',
      'Karambol\Bootstrap\MenuBootstrap',
      'Karambol\Bootstrap\SecurityBootstrap',
      'Karambol\Bootstrap\LocalizationBootstrap',
      'Karambol\Bootstrap\ControllersBootstrap',
      'Karambol\Bootstrap\ConsoleBootstrap',
      'Karambol\Bootstrap\PluginsBootstrap'
    ];

    foreach($bootrappers as $bootstrapClass) {
      $bootstrapper = new $bootstrapClass();
      $bootstrapper->bootstrap($this);
    }

  }

}
