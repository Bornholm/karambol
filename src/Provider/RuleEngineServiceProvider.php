<?php

namespace Karambol\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Karambol\RuleEngine\RuleEngineService;

class RuleEngineServiceProvider implements ServiceProviderInterface
{

    public function register(Application $app) {
      $app['rule_engine'] = new RuleEngineService();
    }

    public function boot(Application $app) {}

}
