<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Provider;
use Karambol\RuleEngine\RuleEngine;
use Silex\Provider\MonologServiceProvider;

class RuleEngineBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {
    // Register rule engine service
    $app->register(new Provider\RuleEngineServiceProvider());
  }

}
