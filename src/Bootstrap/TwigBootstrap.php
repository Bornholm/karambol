<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Silex\Provider\TwigServiceProvider;

class TwigBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {
    // Init Twig view engine
    $app->register(new TwigServiceProvider(), [
      'twig.path' => [__DIR__.'/../Views'],
      'twig.form.templates' => ['bootstrap_3_layout.html.twig']
    ]);
  }

}
