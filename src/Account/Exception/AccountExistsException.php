<?php

namespace Karambol\Account\Exception;

class AccountExistsException extends \Exception
{

  protected $username;

  public function __construct($username) {
    $this->username = $username;
    parent::__construct(sprintf('The account with the username "%s" already exists !', $this->getUsername()), 0);
  }

  public function getUsername() {
    return $this->username;
  }

}
