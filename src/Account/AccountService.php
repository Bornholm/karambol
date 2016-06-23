<?php

namespace Karambol\Account;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Karambol\Entity\BaseUser;
use Karambol\KarambolApp;
use Karambol\Account\ChangePasswordEvent;

class AccountService extends EventDispatcher {

  protected $app;

  public function __construct(KarambolApp $app) {
    $this->app = $app;
  }

  public function createAccount($username, $password) {

    $userEntity = $this->app['user_entity'];
    $orm = $this->app['orm'];

    $salt = $this->generateSalt();
    $hash = $this->hashPassword($password, $salt);

    $user = new $userEntity();

    $user->setUsername($username);
    $user->changePassword($hash, $salt);

    $event = new CreateAccountEvent($user, $password);
    $this->dispatch(CreateAccountEvent::BEFORE_CREATE_ACCOUNT, $event);

    $orm->persist($user);
    $orm->flush();

    $this->dispatch(CreateAccountEvent::AFTER_CREATE_ACCOUNT, $event);

    return $user;

  }

  public function changePassword(BaseUser $user, $newPassword) {

    $orm = $this->app['orm'];

    $salt = $this->generateSalt();
    $hash = $this->hashPassword($newPassword, $salt);

    $user->changePassword($hash, $salt);

    $orm->flush();

    $event = new ChangePasswordEvent($user, $password);
    $this->dispatch(ChangePasswordEvent::NAME, $event);

    return $user;

  }

  protected function generateSalt() {
    return base64_encode(random_bytes(8));
  }

  protected function hashPassword($password, $salt) {
    $encoder = $this->app['security.encoder.digest'];
    return $encoder->encodePassword($password, $salt);
  }

}
