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
      'Karambol\Bootstrap\DoctrineBootstrap',
      'Karambol\Bootstrap\MonologBootstrap',
      'Karambol\Bootstrap\RuleEngineBootstrap',
      'Karambol\Bootstrap\SessionBootstrap',
      'Karambol\Bootstrap\FormBootstrap',
      'Karambol\Bootstrap\TwigBootstrap',
      'Karambol\Bootstrap\SlugifyBootstrap',
      'Karambol\Bootstrap\PageBootstrap',
      'Karambol\Bootstrap\ThemeBootstrap',
      'Karambol\Bootstrap\MenuBootstrap',
      'Karambol\Bootstrap\UrlGeneratorBootstrap',
      'Karambol\Bootstrap\SecurityBootstrap',
      'Karambol\Bootstrap\LocalizationBootstrap',
      'Karambol\Bootstrap\ControllersBootstrap',
      'Karambol\Bootstrap\PluginsBootstrap'
    ];

    foreach($bootrappers as $bootstrapClass) {
      $bootstrapper = new $bootstrapClass();
      $bootstrapper->bootstrap($this);
    }

  }

}
