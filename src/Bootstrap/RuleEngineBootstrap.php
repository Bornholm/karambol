<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Provider;
use Karambol\RuleEngine\RuleEngine;

class RuleEngineBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {
    // Register rule engine service
    $app->register(new Provider\RuleEngineServiceProvider());
  }

}
