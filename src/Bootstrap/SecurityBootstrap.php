<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Silex\Provider\SecurityServiceProvider;
use Karambol\Security\UserProvider;
use Karambol\Security\UserFoundEvent;
use Karambol\RuleEngine\RuleEngine;
use Karambol\AccessControl\RuleEngineAccessControlVoter;
use Karambol\RuleEngine\RuleEngineVariableViewInterface;

class SecurityBootstrap implements BootstrapInterface {

  protected $app;

  public function bootstrap(KarambolApp $app) {

    $this->app = $app;
    $that = $this;

    $app->register(new SecurityServiceProvider(), [
      'security.firewalls' => [
        'main' => [
          'pattern' => '^.*$',
          'anonymous' => true,
          'users' => function() use ($app, $that) {
            $provider = new UserProvider($app);
            return $provider;
          },
          'form' => [
            'login_path' => '/login',
            'check_path' => '/login_check'
          ],
          'logout' => array('logout_path' => '/logout', 'invalidate_session' => true)
        ]
      ],
      'security.access_rules' => [
        ['^/admin', 'IS_AUTHENTICATED_FULLY'],
        ['^/profile', 'IS_AUTHENTICATED_FULLY']
      ]
    ]);

    $app['security.voters'] = $app->share($app->extend('security.voters', function ($voters, $app) {
      $voters[] = new RuleEngineAccessControlVoter($app);
      return $voters;
    }));


  }

}
