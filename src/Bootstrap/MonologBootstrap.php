<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Provider;
use Silex\Provider\MonologServiceProvider;

class MonologBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {

    $loggerConfig = $app['config']['logger'];

    $app->register(new MonologServiceProvider(), [
      'monolog.logfile' => !empty($loggerConfig['file']) ? $loggerConfig['file'] : __DIR__.'/../karambol.log',
      'monolog.level' => !empty($loggerConfig['level']) ? $loggerConfig['level'] : 'debug',
      'monolog.name' => 'karambol',
    ]);

    $app['monolog'] = $app->share($app->extend('monolog', function($monolog, $app) {
      $monolog->pushHandler(new \Monolog\Handler\ErrorLogHandler());
      return $monolog;
    }));

  }

}
