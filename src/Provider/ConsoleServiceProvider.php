<?php

namespace Karambol\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\Console\Application as ConsoleApplication;

class ConsoleServiceProvider implements ServiceProviderInterface
{

  public function register(Application $app) {
    $app['console'] = new ConsoleApplication();
  }

  public function boot(Application $app) {}

}
