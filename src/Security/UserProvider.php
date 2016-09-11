<?php

namespace Karambol\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Karambol\Entity\User;
use Karambol\KarambolApp;
use Karambol\RuleEngine\RuleEngine;
use Karambol\Security\UserFoundEvent;
use Karambol\Security\UserNotFoundEvent;

class UserProvider extends EventDispatcher implements UserProviderInterface {

  public function __construct(KarambolApp $app) {
    $this->app = $app;
  }

  public function loadUserByUsername($username) {

    $orm = $this->app['orm'];

    $usersRepo = $orm->getRepository(User::class);
    $user = $usersRepo->findOneByUsername($username);

    if(!$user) {
      $event = new UserNotFoundEvent($username);
      $this->dispatch(UserNotFoundEvent::NAME, $event);
      throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
    }

    $event = new UserFoundEvent($user);
    $this->dispatch(UserFoundEvent::NAME, $event);

    return $user;

  }

  public function refreshUser(UserInterface $user) {

    if (!$user instanceof User) {
      throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
    }

    return $this->loadUserByUsername($user->getUsername());

  }

  public function supportsClass($class) {
    return $class === User::class;
  }

}
