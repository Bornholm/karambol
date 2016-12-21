<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Provider;
use Silex\Provider\MonologServiceProvider;

class MonologBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {

    $loggerConfig = $app['config']['logger'];

    $logFile = php_sapi_name() == 'cli' ? $loggerConfig['cli_file'] : $loggerConfig['file'];

    $app->register(new MonologServiceProvider(), [
      'monolog.logfile' => empty($logFile) ? 'php://stdout' : $logFile,
      'monolog.level' => $loggerConfig['level'],
      'monolog.name' => 'karambol',
      'monolog.use_error_handler' => true
    ]);
  }

}
