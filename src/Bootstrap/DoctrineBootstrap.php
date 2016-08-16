<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Provider;
use Karambol\Provider\UserEntityProvider;

class DoctrineBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {

    $app->register(new UserEntityProvider());

    $databaseConfig = $app['config']['database'];
    $debug = $app['config']['debug'];
    $config['orm.entities'] = [__DIR__.'/..'];

    $app->register(new Provider\DoctrineORMServiceProvider($config['orm.entities'], $databaseConfig, $debug));

  }

}
