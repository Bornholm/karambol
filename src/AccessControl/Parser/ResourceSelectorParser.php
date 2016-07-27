<?php

namespace Karambol\AccessControl\Parser;

use Karambol\AccessControl\ResourceSelector;
use Karambol\AccessControl\Parser\ResourceSelectorTokenizer;

class ResourceSelectorParser {

  public function parse($selectorStr) {

    $tokenizer = new ResourceSelectorTokenizer();
    $tokens = $tokenizer->tokenize($selectorStr);

    $hasResourceTypeToken = isset($tokens[0]) && $tokens[0]['token'] === ResourceSelectorTokenizer::TOKEN_RESOURCE;
    $hasOwnerToken = isset($tokens[1]) && $tokens[1]['token'] === ResourceSelectorTokenizer::TOKEN_OWNER;

    if(!$hasResourceTypeToken) return null;

    $selector = new ResourceSelector(
      $tokens[0]['type'],
      $tokens[0]['references'],
      $hasOwnerToken ? $tokens[1]['references'] : []
    );

    return $selector;

  }

}
