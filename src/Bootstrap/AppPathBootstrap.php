<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Provider\AppPathProvider;

class AppPathBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {
    $app->register(new AppPathProvider());
  }

}
