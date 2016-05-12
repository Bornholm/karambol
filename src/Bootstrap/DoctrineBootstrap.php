<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Provider;

class DoctrineBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {

    $databaseConfig = $app['config']['database'];
    $debug = $app['config']['debug'];
    $config['orm.entities'] = [__DIR__];
    $app->register(new Provider\DoctrineORMServiceProvider($config['orm.entities'], $databaseConfig, $debug));

  }

}
