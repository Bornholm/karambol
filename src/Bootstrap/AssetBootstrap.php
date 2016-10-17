<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Provider;
use Karambol\Asset\Twig\AssetExtension;

class AssetBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {

    // Register asset service
    $app->register(new Provider\AssetServiceProvider());

    // Add twig extension
    $app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
      $twig->addExtension(new AssetExtension($app));
      return $twig;
    }));

  }

}
