<?php

namespace Karambol\AccessControl\Parser;


class TokenizerException extends \Exception {

  protected $characterPosition;
  protected $criteria;

  public function __construct($criteria, $characterPosition, $code = 0, Exception $previous = null) {
    $this->criteria = $criteria;
    $this->characterPosition = $characterPosition;
    $message = sprintf(
      'Invalid character "%s" found at position %s while tokenizing criteria "%s" !',
      $this->getCharacter(),
      $characterPosition,
      $criteria
    );
    parent::__construct($message, $code, $previous);
  }

  public function getCriteria() {
    return $this->criteria;
  }

  public function getCharacterPosition() {
    return $this->characterPosition;
  }

  public function getCharacter() {
    return $this->criteria[$this->characterPosition];
  }

}
