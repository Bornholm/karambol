<?php

namespace Karambol\Controller;

use Karambol\KarambolApp;

abstract class Controller implements ControllerInterface {

  protected $app;

  public function setApp(KarambolApp $app) {
    $this->app = $app;
    return $this;
  }

  public function getApp() {
    return $this->app;
  }

  public function get($service) {
    return $this->app[$service];
  }

  abstract public function mount();

}