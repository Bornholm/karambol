<?php

namespace Karambol\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Karambol\Entity\User;
use Karambol\KarambolApp;

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

}
