<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Silex\Provider\SessionServiceProvider;

class SessionBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {
    $app->register(new SessionServiceProvider());
  }

}
