<?php

  require_once __DIR__.'/../vendor/autoload.php';

  // Instanciate application
  $app = new Silex\Application();
  // Bootstrap application
  require_once __DIR__.'/bootstrap.php';
  // Load routes
  require_once __DIR__.'/routes.php';

  return $app;
