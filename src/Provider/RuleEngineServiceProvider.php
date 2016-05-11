<?php

namespace Karambol\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Karambol\RuleEngine\RuleEngineService;

class RuleEngineServiceProvider implements ServiceProviderInterface
{

    public function register(Application $app) {
      $app['rule.engine'] = new RuleEngineService($app['orm']);
    }

    public function boot(Application $app) {}

}
