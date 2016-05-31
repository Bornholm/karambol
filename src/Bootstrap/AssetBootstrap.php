<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Provider;

class AssetBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {
    // Register asset service
    $app->register(new Provider\AssetServiceProvider());
  }

}
