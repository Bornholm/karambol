<?php

  // Init Twig view engine
  $app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
  ));

  $app['twig'] = $app->share($app->extend('twig', function($twig, $app) {

    $twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) use ($app) {
      $req = $app['request'];
      $baseUrl = $req->getBasePath();
      return sprintf($baseUrl.'/%s', ltrim($asset, '/'));
    }));

    return $twig;

  }));
