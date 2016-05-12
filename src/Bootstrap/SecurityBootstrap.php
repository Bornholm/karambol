<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Silex\Provider\SecurityServiceProvider;

class SecurityBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {
    $app->register(new SecurityServiceProvider(), [
      'security.firewalls' => [
        'admin' => [
          'pattern' => '^/admin',
          'http' => true,
          'users' => [
              // raw password is foo
              'admin' => ['ROLE_ADMIN', '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg=='],
          ]
        ]
      ]
    ]);
  }

}
