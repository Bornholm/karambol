<?php

namespace Karambol\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class UserEntityProvider implements ServiceProviderInterface
{

    public function register(Application $app) {
      $app['user_entity'] = 'Karambol\Entity\User';
    }

    public function boot(Application $app) {}

}
