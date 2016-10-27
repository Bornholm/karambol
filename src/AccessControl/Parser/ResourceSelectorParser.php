<?php

namespace Karambol\AccessControl\Parser;

use Karambol\AccessControl\ResourceSelector;
use Karambol\AccessControl\Parser\ResourceSelectorTokenizer;

class ResourceSelectorParser {

  public function parse($selectorStr) {

    $tokenizer = new ResourceSelectorTokenizer();
    $tokens = $tokenizer->tokenize($selectorStr);

    $hasResourceToken = isset($tokens[0]) && $tokens[0]['token'] === ResourceSelectorTokenizer::TOKEN_RESOURCE;

    if(!$hasResourceToken) return null;

    $resourceToken = $tokens[0];

    $selector = new ResourceSelector(
      $resourceToken['type'],
      $resourceToken['references'],
      !empty($resourceToken['property']) ? $resourceToken['property'] : null
    );

    return $selector;

  }

}
