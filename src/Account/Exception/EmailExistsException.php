<?php

namespace Karambol\Account\Exception;

class EmailExistsException extends \Exception
{

  protected $email;

  public function __construct($email) {
    $this->email = $email;
    parent::__construct(sprintf('The email "%s" is already associated to an account !', $this->getEmail()), 0);
  }

  public function getEmail() {
    return $this->email;
  }

}
