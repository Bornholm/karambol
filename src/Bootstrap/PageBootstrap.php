<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Provider\PageServiceProvider;

class PageBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {
    $app->register(new PageServiceProvider());
  }

}
