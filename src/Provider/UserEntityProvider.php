<?php

namespace Karambol\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Karambol\Entity\User;

class UserEntityProvider implements ServiceProviderInterface
{

    public function register(Application $app) {
      $app['user_entity'] = User::class;
    }

    public function boot(Application $app) {}

}
