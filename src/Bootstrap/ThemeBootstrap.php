<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Provider;
use Karambol\Theme;

class ThemeBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {

    // Register theme service
    $app->register(new Provider\ThemeServiceProvider());

    $themeListener = new Theme\ThemeListener($app);
    $app['theme']->addListener(
      Theme\ThemeService::THEME_CHANGE,
      [$themeListener, 'onThemeChange']
    );

    $app['theme']->setSelectedTheme(null);

  }

}
