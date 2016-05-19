<?php

namespace Karambol\RuleEngine;

use Symfony\Component\ExpressionLanguage\ParserCache\ParserCacheInterface;
use Symfony\Component\ExpressionLanguage\ParsedExpression;
use Doctrine\Common\Cache;

class ParserCache implements ParserCacheInterface {

  protected $cache;

  public function __construct() {
    if (extension_loaded('apc')) {
      $this->cache = new Cache\ApcCache();
    } elseif (extension_loaded('xcache')) {
      $this->cache = new Cache\XcacheCache();
    } else {
      $this->cache = new Cache\ArrayCache();
    }
    $this->cache = new Cache\ArrayCache();
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
