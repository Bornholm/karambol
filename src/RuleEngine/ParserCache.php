<?php

namespace Karambol\RuleEngine;

use Symfony\Component\ExpressionLanguage\ParserCache\ParserCacheInterface;
use Symfony\Component\ExpressionLanguage\ParsedExpression;
use Karambol\Util\CacheFactory;

class ParserCache implements ParserCacheInterface {

  protected $cache;

  public function __construct() {
    $this->cache = CacheFactory::create();
  }

  public function fetch($key) {
    if (false === $value = $this->cache->fetch($key)) {
      return;
    }
    return $value;
  }

  public function save($key, ParsedExpression $expression) {
    return $this->cache->save($key, $expression);
  }

}
