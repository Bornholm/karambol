<?php

namespace Karambol\Test;

use Karambol\KarambolApp;

trait KarambolTestTrait {

  /**
   * @var KarambolApp
   */
  protected $app;

  public function setUp() {
    $app = new KarambolApp();
    $app->boot();
    $this->app = $app;
  }

}
