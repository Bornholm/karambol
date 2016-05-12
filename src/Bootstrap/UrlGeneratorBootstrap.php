<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Silex\Provider\UrlGeneratorServiceProvider;

class UrlGeneratorBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {
    $app->register(new UrlGeneratorServiceProvider());
  }

}
