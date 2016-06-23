<?php

namespace Karambol\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Karambol\Account\AccountService;

class AccountServiceProvider implements ServiceProviderInterface
{

    public function register(Application $app) {
      $app['accounts'] = new AccountService($app);
    }

    public function boot(Application $app) {}

}
