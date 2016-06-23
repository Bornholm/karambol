<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Silex\Provider\SecurityServiceProvider;
use Karambol\Security\UserProvider;
use Karambol\Security\UserFoundEvent;
use Karambol\RuleEngine\RuleEngineService;

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
            $provider->addListener(UserFoundEvent::NAME, [$that, 'executeAccessControlRules']);
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
        ['^/admin', 'ROLE_ADMIN'],
        ['^/profile', 'IS_AUTHENTICATED_FULLY']
      ]
    ]);


  }

  public function executeAccessControlRules(UserFoundEvent $event) {

    $user = $event->getUser();
    $app = $this->app;

    $logger = $app['monolog'];
    $ruleEngine = $app['rule_engine'];
    $rulesetRepo = $app['orm']->getRepository('Karambol\Entity\RuleSet');

    $ruleset = $rulesetRepo->findOneByName(RuleEngineService::ACCESS_CONTROL);

    if(!$ruleset) return;

    $vars = [
      '_user' => $user,
      'user' => $user->toPOPO()
    ];

    $rules = $ruleset->getRules()->toArray();

    try {
      $ruleEngine->execute(RuleEngineService::ACCESS_CONTROL, $rules, $vars);
    } catch(\Exception $ex) {
      $logger->error($ex);
    }

  }

}
