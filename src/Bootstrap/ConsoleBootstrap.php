<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Provider\ConsoleServiceProvider;
use Karambol\Command;

class ConsoleBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {

    $app->register(new ConsoleServiceProvider());

    // Register default commands
    $console = $app['console'];

    $console->add(new Command\LinkAssetsCommand($app));
    $console->add(new Command\CreateUserCommand($app));
    $console->add(new Command\PromoteUserCommand($app));

  }

}
