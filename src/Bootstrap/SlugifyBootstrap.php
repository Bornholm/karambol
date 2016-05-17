<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Cocur\Slugify\Bridge\Silex\SlugifyServiceProvider;

class SlugifyBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {
    $app->register(new SlugifyServiceProvider());
  }

}
