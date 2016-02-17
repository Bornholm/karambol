<?php

namespace Karambol\Controller;

use Karambol\KarambolApp;

abstract class Controller implements ControllerInterface {

  protected $app;

  public function get($service) {
    return $this->app[$service];
  }

  public function bindTo(KarambolApp $app) {
    $this->app = $app;
    $this->mount($app);
  }

  abstract public function mount(KarambolApp $app);

}
