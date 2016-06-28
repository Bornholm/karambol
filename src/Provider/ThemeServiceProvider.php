<?php

namespace Karambol\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Karambol\Theme\ThemeService;

class ThemeServiceProvider implements ServiceProviderInterface
{

    public function register(Application $app) {

      $themeConfig = $app['config']['theme'];

      $app['themes'] = new ThemeService(
        __DIR__.'/../../themes',
        $themeConfig['availableThemes']
      );

    }

    public function boot(Application $app) {}

}
