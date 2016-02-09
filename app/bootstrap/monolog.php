<?php

  $loggerConfig = $app['config']['logger'];

  $app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => !empty($loggerConfig['file']) ? $loggerConfig['file'] : __DIR__.'/../../karambol.log',
    'monolog.level' => !empty($loggerConfig['level']) ? $loggerConfig['level'] : 'debug',
    'monolog.name' => 'karambol',
  ));
