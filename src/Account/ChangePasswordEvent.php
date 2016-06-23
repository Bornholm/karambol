<?php

namespace Karambol\Account;

use Symfony\Component\EventDispatcher\Event;
use Karambol\Entity\BaseUser;

class ChangePasswordEvent extends Event {

  const NAME = 'account.change_password';

  protected $user;
  protected $clearTextPassword;

  public function __construct(BaseUser $user, $clearTextPassword) {
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
