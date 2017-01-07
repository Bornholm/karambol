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
 * Tokenize une ressource
 * @package Karambol
 * @license AGPLv3
 * @author William Petit
 */
class ResourceSelectorTokenizer {

  /**
   * Separateur
   * @var string
   */
  const PROPERTY_SEPARATOR = '.';
  
  /**
   * Ouverture collection
   * @var string
   */
  const ID_SET_OPENING = '[';
  
  /**
   * Fermeture collection
   * @var string
   */
  const ID_SET_CLOSING = ']';
  
  /**
   * Separateur collection
   * @var string
   */
  const ID_SEPARATOR = ',';
  
  /**
   * Willcard
   * @var string
   */
  const WILDCARD = '*';
  
  /**
   * @todo ecrire description
   * @var int
   */
  const STATE_BEGIN = 0;
  
  /**
   * @todo ecrire description
   * @var int
   */
  const STATE_RESOURCE_TYPE_DEFINITION = 1;
  
  /**
   * @todo ecrire description
   * @var int
   */
  const STATE_RESOURCE_PROPERTY_DEFINITION = 2;
  
  /**
   * @todo ecrire description
   * @var int
   */
  const STATE_ID_LIST = 3;
  
  /**
   * @todo ecrire description
   * @var int
   */
  const STATE_END = 4;
  
  /**
   * @todo ecrire description
   * @var string
   */
  const TOKEN_RESOURCE = 'resource';
  
  /**
   * Tokenize une chaine
   * @param string $selectorStr Chaine 
   * @return array
   */
  public function tokenize($selectorStr) {

    $state = self::STATE_BEGIN;
    $tokens = [];
    $current = [];

    if(empty($selectorStr)) return $tokens;

    $strLength = mb_strlen($selectorStr);
    $charIndex = 0;

    while($charIndex < $strLength) {

      $char = $selectorStr[$charIndex];

      switch($state) {

        case self::STATE_BEGIN:

          $this->checkInvalidResourceTypeChar($selectorStr, $charIndex);

          $current = [ 'token' => self::TOKEN_RESOURCE, 'type' => '', 'references' => [], 'property' => '' ];
          if($char === self::ID_SET_OPENING) $this->throwInvalidCharException($selectorStr, $charIndex);
          $state = self::STATE_RESOURCE_TYPE_DEFINITION;

          break;

        case self::STATE_RESOURCE_TYPE_DEFINITION:

          $this->checkInvalidResourceTypeChar($selectorStr, $charIndex);

          if($char === self::ID_SET_OPENING) {
            $state = self::STATE_ID_LIST;
            $charIndex++;
            continue;
          }

          if($char === self::PROPERTY_SEPARATOR) {
            $state = self::STATE_RESOURCE_PROPERTY_DEFINITION;
            $charIndex++;
            continue;
          }

          $current['type'] .= $char;
          $charIndex++;

          if($charIndex === $strLength) {
            $tokens[] = $current;
            $state = self::STATE_END;
          }

          break;

        case self::STATE_RESOURCE_PROPERTY_DEFINITION:

          $this->checkInvalidResourceTypeChar($selectorStr, $charIndex);

          if($char === self::PROPERTY_SEPARATOR) {
            $this->throwInvalidCharException($selectorStr, $charIndex);
          }

          if($char === self::ID_SET_OPENING) {
            $state = self::STATE_ID_LIST;
            $charIndex++;
            continue;
          }

          $current['property'] .= $char;
          $charIndex++;

          if($charIndex === $strLength) {
            $tokens[] = $current;
            $state = self::STATE_END;
          }

          break;


        case self::STATE_ID_LIST:

          if($char === self::ID_SET_CLOSING) {

            $this->trimLast($current['references']);
            $tokens[] = $current;
            $charIndex++;
            $state = self::STATE_END;
            continue;

          }

          if($char === self::ID_SEPARATOR) {
            $this->trimLast($current['references']);
            $current['references'][] = '';
            $charIndex++;
            continue;
          }

          if(count($current['references']) === 0) $current['references'][] = '';
          $current['references'][count($current['references'])-1] .= $char;
          $charIndex++;

          break;

        case self::STATE_END:
          if($charIndex !== $strLength-1) $this->throwInvalidCharException($selectorStr, $charIndex);
          break;

      }

    }

    return $tokens;

  }
  
  /**
   * Chaine
   * @param string $selector
   * @param int $charIndex index dans la chaine
   * @return array|TokenizerException
   */
  protected function checkInvalidResourceTypeChar($selector, $charIndex) {
    return in_array($selector[$charIndex], [
      self::ID_SET_CLOSING,
      self::ID_SEPARATOR
    ]);
    if($isInvalidChar) $this->throwInvalidCharException($selector, $charIndex);
  }

  /**
   * Exception
   * @param string $selector
   * @param int $charIndex
   * @throws TokenizerException
   */
  protected function throwInvalidCharException($selector, $charIndex) {
    throw new TokenizerException($selector, $charIndex);
  }
  
  /**
   * Nettoie le dernier element d'une tableau
   * @param array $arr
   */
  protected function trimLast(&$arr) {
    $lastIndex = count($arr)-1;
    $arr[$lastIndex] = trim($arr[$lastIndex]);
  }

}
