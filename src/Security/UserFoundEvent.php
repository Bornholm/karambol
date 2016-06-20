<?php

namespace Karambol\Security;
use Symfony\Component\EventDispatcher\Event;
use Karambol\Entity\User;

class UserFoundEvent extends Event {

  const NAME = 'user_provider.user_found';

  protected $user;

  public function __construct(User $user) {
    $this->user = $user;
  }

  public function getUser() {
    return $this->user;
  }

}
