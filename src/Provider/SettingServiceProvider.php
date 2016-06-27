<?php

namespace Karambol\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Karambol\Setting\SettingService;

class SettingServiceProvider implements ServiceProviderInterface
{

    public function register(Application $app) {
      $app['settings'] = new SettingService($app);
    }

    public function boot(Application $app) {}

}
