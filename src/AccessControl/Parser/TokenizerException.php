<?php

namespace Karambol\AccessControl\Parser;


class TokenizerException extends \Exception {

  protected $characterPosition;
  protected $selector;

  public function __construct($selector, $characterPosition, $code = 0, Exception $previous = null) {
    $this->selector = $selector;
    $this->characterPosition = $characterPosition;
    $message = sprintf(
      'Invalid character "%s" found at position %s while tokenizing selector "%s" !',
      $this->getCharacter(),
      $characterPosition,
      $selector
    );
    parent::__construct($message, $code, $previous);
  }

  public function getSelector() {
    return $this->selector;
  }

  public function getCharacterPosition() {
    return $this->characterPosition;
  }

  public function getCharacter() {
    return $this->selector[$this->characterPosition];
  }

}
