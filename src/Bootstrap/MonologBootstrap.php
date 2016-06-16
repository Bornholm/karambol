<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Provider;
use Silex\Provider\MonologServiceProvider;

class MonologBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {

    $loggerConfig = $app['config']['logger'];

    $app->register(new MonologServiceProvider(), [
      'monolog.logfile' => empty($loggerConfig['file']) ? 'php://stdout' : $loggerConfig['file'],
      'monolog.level' => $loggerConfig['level'],
      'monolog.name' => 'karambol',
      'monolog.use_error_handler' => true
    ]);
  }

}
