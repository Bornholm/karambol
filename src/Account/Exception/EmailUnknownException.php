<?php

namespace Karambol\Account\Exception;

class EmailUnknownException extends \Exception
{

  protected $username;

  public function __construct($username) {
    $this->username = $username;
    parent::__construct(sprintf('The account with the username "%s" does not have an email address !', $this->getUsername()), 0);
  }

  public function getUsername() {
    return $this->username;
  }

}
