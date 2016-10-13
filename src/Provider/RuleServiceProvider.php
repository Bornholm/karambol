<?php

namespace Karambol\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Karambol\RuleEngine\RuleEngine;

class RuleServiceProvider implements ServiceProviderInterface
{

    public function register(Application $app) {
      $app['rule_engine'] = new RuleEngine();
    }

    public function boot(Application $app) {}

}
