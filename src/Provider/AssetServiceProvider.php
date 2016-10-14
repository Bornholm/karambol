<?php

namespace Karambol\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Karambol\Asset\AssetService;

class AssetServiceProvider implements ServiceProviderInterface
{

    public function register(Application $app) {
      $app['assets'] = new AssetService($app['app_path']);
    }

    public function boot(Application $app) {}

}
