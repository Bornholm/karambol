<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\ValidatorServiceProvider;

class FormBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {
    $app->register(new ValidatorServiceProvider());
    $app->register(new FormServiceProvider());
  }

}
