<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Silex\Provider\SecurityServiceProvider;

class SecurityBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {
    $app->register(new SecurityServiceProvider(), [
      'security.firewalls' => [
        'main' => [
          'pattern' => '^.*$',
          'anonymous' => true,
          'users' => [
            // raw password is foo
            'admin' => ['ROLE_ADMIN', '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg=='],
          ],
          'form' => [
            'login_path' => '/login',
            'check_path' => '/login_check'
          ],
          'logout' => array('logout_path' => '/logout', 'invalidate_session' => true)
        ]
      ],
      'security.access_rules' => [
        ['^/admin', 'ROLE_ADMIN']
      ]
    ]);
  }

}
