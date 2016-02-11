<?php

namespace Karambol\Controller;

use Karambol\KarambolApp;

abstract class Controller implements ControllerInterface {

  protected $app;

  public function get($service) {
    return $this->app[$service];
  }

  public function mount(KarambolApp $app) {
    $this->app = $app;
    $this->_mount($app);
  }

  abstract protected function _mount(KarambolApp $app);

}
