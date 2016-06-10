<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Provider\ConsoleServiceProvider;
use Karambol\Command\LinkAssetsCommand;

class ConsoleBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {

    $app->register(new ConsoleServiceProvider());

    // Register default commands
    $app['console']->add(new LinkAssetsCommand($app));

  }

}
