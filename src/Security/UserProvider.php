<?php

namespace Karambol\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Karambol\Entity\User;
use Karambol\KarambolApp;
use Karambol\RuleEngine\RuleEngineService;

class UserProvider implements UserProviderInterface {

  public function __construct(KarambolApp $app) {
    $this->app = $app;
  }

  public function loadUserByUsername($username) {

    $orm = $this->app['orm'];
    $usersRepo = $orm->getRepository('Karambol\Entity\User');
    $user = $usersRepo->findOneByEmail($username);

    if(!$user) {
      throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
    }

    $this->executeAccessControlRules($user);

    return $user;

  }

  public function refreshUser(UserInterface $user) {

    if (!$user instanceof User) {
      throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
    }

    return $this->loadUserByUsername($user->getUsername());

  }

  public function supportsClass($class) {
    return $class === 'Karambol\Entity\User';
  }

  protected function executeAccessControlRules(User $user) {

    $app = $this->app;

    $logger = $app['monolog'];
    $ruleEngine = $app['rule_engine'];
    $rulesetRepo = $app['orm']->getRepository('Karambol\Entity\RuleSet');

    $ruleset = $rulesetRepo->findOneByName(RuleEngineService::ACCESS_CONTROL);

    if(!$ruleset) return;

    $vars = [
      '_user' => $user,
      'user' => $user->toAPIObject()
    ];

    $rules = $ruleset->getRules()->toArray();

    try {
      $ruleEngine->execute(RuleEngineService::ACCESS_CONTROL, $rules, $vars);
    } catch(\Exception $ex) {
      $logger->error($ex);
    }

  }

}
