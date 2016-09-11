<?php

namespace Karambol\Account;

use Symfony\Component\EventDispatcher\Event;
use Karambol\Entity\User;

class CreateAccountEvent extends Event {

  const BEFORE_CREATE_ACCOUNT = 'account.before_create';
  const AFTER_CREATE_ACCOUNT = 'account.after_create';

  protected $user;
  protected $clearTextPassword;

  public function __construct(User $user, $clearTextPassword) {
    $this->user = $user;
    $this->clearTextPassword = $clearTextPassword;
  }

  public function getUser() {
    return $this->user;
  }

  public function getClearTextPassword() {
    return $clearTextPassword;
  }

}
