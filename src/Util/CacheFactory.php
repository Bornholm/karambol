<?php

namespace Karambol\Util;

use Doctrine\Common\Cache;

class CacheFactory {

  /**
   * Return a new cache instance based on available extensions (APCU, APC, XCache or Array)
   *
   * @return Doctrine\Common\Cache
   */
  public static function create() {
    if (extension_loaded('apcu')) {
      return new Cache\ApcuCache();
    } elseif (extension_loaded('apc')) {
      return new Cache\ApcCache();
    } elseif (extension_loaded('xcache')) {
      return new Cache\XcacheCache();
    } else {
      return new Cache\ArrayCache();
    }
  }

}
