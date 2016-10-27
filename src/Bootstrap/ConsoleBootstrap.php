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
    $console->add(new Command\CreateAccountCommand($app));
    $console->add(new Command\PromoteAccountCommand($app));
    $console->add(new Command\PluginConfigCommand($app));
    $console->add(new Command\DumpRulesCommand($app));
    $console->add(new Command\LoadRulesCommand($app));
    $console->add(new Command\ClearCacheCommand($app));

  }

}
