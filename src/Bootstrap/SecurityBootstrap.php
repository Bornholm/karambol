<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Silex\Provider\SecurityServiceProvider;
use Karambol\Security\UserProvider;

class SecurityBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {
    $app->register(new SecurityServiceProvider(), [
      'security.firewalls' => [
        'main' => [
          'pattern' => '^.*$',
          'anonymous' => true,
          'users' => function() use ($app) {
            return new UserProvider($app);
          },
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
