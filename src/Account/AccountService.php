<?php

namespace Karambol\Account;

use Karambol\Entity\User;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Karambol\KarambolApp;
use Karambol\Account\ChangePasswordEvent;

class AccountService extends EventDispatcher {

  protected $app;

  public function __construct(KarambolApp $app) {
    $this->app = $app;
  }

  public function createAccount($username, $password) {

    if($this->accountExists($username)) {
      throw new Exception\AccountExistsException($username);
    }

    $orm = $this->app['orm'];

    $salt = $this->generateSalt();
    $hash = $this->hashPassword($password, $salt);

    $user = new User();

    $user->setUsername($username);
    $user->changePassword($hash, $salt);

    $event = new CreateAccountEvent($user, $password);
    $this->dispatch(CreateAccountEvent::BEFORE_CREATE_ACCOUNT, $event);

    $orm->persist($user);
    $orm->flush();

    $this->dispatch(CreateAccountEvent::AFTER_CREATE_ACCOUNT, $event);

    return $user;

  }

  public function accountExists($username) {
    $orm = $this->app['orm'];
    $qb = $orm->getRepository(User::class)->createQueryBuilder('u');
    $qb->select('count(u)')
      ->where($qb->expr()->eq('u.username', $qb->expr()->literal($username)))
    ;
    return $qb->getQuery()->getSingleScalarResult() != 0;
  }

  public function changePassword(User $user, $newPassword) {

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
