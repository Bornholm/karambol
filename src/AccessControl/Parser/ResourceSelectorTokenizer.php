<?php

namespace Karambol\AccessControl\Parser;


class ResourceSelectorTokenizer {

  const ID_SET_OPENING = '[';
  const ID_SET_CLOSING = ']';
  const ID_SEPARATOR = ',';
  const WILDCARD = '*';

  const STATE_BEGIN = 0;
  const STATE_RESOURCE_TYPE_DEFINITION = 1;
  const STATE_ID_LIST = 2;
  const STATE_END = 3;

  const TOKEN_RESOURCE = 'resource';

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
          $current = [ 'token' => self::TOKEN_RESOURCE, 'type' => '', 'references' => [] ];
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

          $current['type'] .= $char;
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

  protected function checkInvalidResourceTypeChar($selector, $charIndex) {
    return in_array($selector[$charIndex], [
      self::ID_SET_CLOSING,
      self::ID_SEPARATOR
    ]);
    if($isInvalidChar) $this->throwInvalidCharException($selector, $charIndex);
  }

  protected function throwInvalidCharException($selector, $charIndex) {
    throw new TokenizerException($selector, $charIndex);
  }

  protected function trimLast(&$arr) {
    $lastIndex = count($arr)-1;
    $arr[$lastIndex] = trim($arr[$lastIndex]);
  }

}
