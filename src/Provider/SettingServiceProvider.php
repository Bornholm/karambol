<?php

namespace Karambol\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Karambol\Setting\SettingService;

class SettingServiceProvider implements ServiceProviderInterface
{

  public function register(Application $app) {
    $settingsValues = isset($app['config']['settings']) ? $app['config']['settings'] : [];
    $app['settings'] = new SettingService(__DIR__.'/../../config/local.d/_settings.yml', $settingsValues);
  }

  public function boot(Application $app) {}

}
