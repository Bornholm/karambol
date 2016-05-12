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

    $app['twig'] = $app->share($app->extend('twig', function($twig, $app) {

      $twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) use ($app) {
        $req = $app['request'];
        $baseUrl = $req->getBasePath();
        return sprintf($baseUrl.'/%s', ltrim($asset, '/'));
      }));

      return $twig;

    }));
  }

}
