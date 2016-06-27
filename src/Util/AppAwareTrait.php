<?php

namespace Karambol\Util;
use Karambol\KarambolApp;

trait AppAwareTrait {

  protected $app;

  public function __construct(KarambolApp $app) {
    $this->app = $app;
  }

}
