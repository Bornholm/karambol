<?php

namespace Karambol\Security;
use Symfony\Component\EventDispatcher\Event;
use Karambol\Entity\User;

class UserNotFoundEvent extends Event {

  const NAME = 'user_provider.user_not_found';

  protected $username;

  public function __construct($username) {
    $this->username = $username;
  }

  public function getUsername() {
    return $this->username;
  }

}
