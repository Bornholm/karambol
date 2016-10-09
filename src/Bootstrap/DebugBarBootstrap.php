<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Provider\DebugBarProvider;
use Karambol\Twig\DebugBarExtension;

class DebugBarBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {

    $app->register(new DebugBarProvider());

    $app->share($app->extend('twig', function($twig) use ($app) {
      $twig->addExtension(new DebugBarExtension($app));
      return $twig;
    }));
  }

}
