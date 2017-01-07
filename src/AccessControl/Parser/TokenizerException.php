<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Karambol\AccessControl\Parser;

/**
 * Exception
 * @package Karambol
 * @license AGPLv3
 * @author William Petit
 */
class TokenizerException extends \Exception {
  
  /**
   * Position dans la chaine
   * @var int
   */
  protected $characterPosition;
  
  /**
   * Chaine de caractere
   * @var string
   */
  protected $selector;

  /**
   * Constructeur de classe
   * @param string $selector
   * @param int $characterPosition
   * @param int $code
   * @param \Karambol\AccessControl\Parser\Exception $previous
   */
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

  /**
   * Retourne la chaine de caractere
   * @return string
   */
  public function getSelector() {
    return $this->selector;
  }
  
  /**
   * Retourne la position dans la chaine
   * @return int
   */
  public function getCharacterPosition() {
    return $this->characterPosition;
  }
  
  /**
   * Retourne le caractere dans la chaine
   * @return string
   */
  public function getCharacter() {
    return $this->selector[$this->characterPosition];
  }

}
