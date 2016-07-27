<?php

namespace Karambol\AccessControl\Parser;


class ResourceSelectorTokenizer {

  const ID_SET_OPENING = '[';
  const ID_SET_CLOSING = ']';
  const ID_SEPARATOR = ',';
  const OWNER_PREFIX = '@';
  const OWNER_SELF = 'self';
  const WILDCARD = '*';

  const STATE_BEGIN = 0;
  const STATE_RESOURCE_TYPE_DEFINITION = 1;
  const STATE_ID_LIST = 2;
  const STATE_OWNER_DEFINITION = 3;
  const STATE_END = 4;

  const TOKEN_RESOURCE = 'resource';
  const TOKEN_OWNER = 'owner';

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

          if($char === self::OWNER_PREFIX) {
            $tokens[] = $current;
            $current = [ 'token' => self::TOKEN_OWNER, 'references' => [] ];
            $state = self::STATE_OWNER_DEFINITION;
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

            $tokens[] = $current;
            $charIndex++;

            if($current['token'] === self::TOKEN_RESOURCE) {
              if($charIndex < $strLength && $selectorStr[$charIndex] === self::OWNER_PREFIX) {
                $current = [ 'token' => self::TOKEN_OWNER, 'references' => [] ];
                $state = self::STATE_OWNER_DEFINITION;
              } else {
                $state = self::STATE_OWNER_DEFINITION;
              }
              $charIndex++;
              continue;
            }

            if($current['token'] === self::TOKEN_OWNER) {
              $state = self::STATE_END;
              continue;
            }

          }

          if($char === self::ID_SEPARATOR) {
            $current['references'][] = '';
            $charIndex++;
            continue;
          }

          if(count($current['references']) === 0) $current['references'][] = '';
          $current['references'][count($current['references'])-1] .= $char;
          $charIndex++;

          break;

          case self::STATE_OWNER_DEFINITION:

            $ownerSelfLen = mb_strlen(self::OWNER_SELF);
            $hasSelf = mb_substr($selectorStr, $charIndex, $ownerSelfLen) === self::OWNER_SELF;

            if($char === self::ID_SET_OPENING) {
              $state = self::STATE_ID_LIST;
              $charIndex++;
              continue;
            }

            if($hasSelf) {
              $current['references'][] = self::OWNER_SELF;
              $charIndex += $ownerSelfLen;
              $tokens[] = $current;
              $state = self::STATE_END;
              continue;
            }

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
      self::ID_SEPARATOR,
      self::OWNER_PREFIX
    ]);
    if($isInvalidChar) $this->throwInvalidCharException($selector, $charIndex);
  }

  protected function throwInvalidCharException($selector, $charIndex) {
    throw new TokenizerException($selector, $charIndex);
  }

}
