<?php

namespace Karambol\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Karambol\RuleEngine\RuleEngine;
use Karambol\RuleEngine\RuleDumper;

class RuleServiceProvider implements ServiceProviderInterface
{

    public function register(Application $app) {
      $app['rule_engine'] = new RuleEngine();
      $app['rule_dumper'] = new RuleDumper($app['orm']);
    }

    public function boot(Application $app) {}

}
