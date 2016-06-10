<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Provider\ConsoleServiceProvider;
use Karambol\Command;

class ConsoleBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {

    $app->register(new ConsoleServiceProvider());

    // Register default commands
    $app['console']->add(new Command\LinkAssetsCommand($app));
    $app['console']->add(new Command\CreateUserCommand($app));

  }

}
