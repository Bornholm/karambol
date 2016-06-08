<?php

namespace Karambol\Controller;

use Karambol\KarambolApp;

abstract class Controller implements ControllerInterface {

  protected $app;

  public function get($service) {
    return isset($this->app[$service]) ? $this->app[$service] : null;
  }

  public function redirect($url, $status = 302) {
    return $this->app->redirect($url, $status);
  }

  public function abort($status, $message = '') {
    return $this->app->abort($status, $message);
  }

  public function bindTo(KarambolApp $app) {
    $this->app = $app;
    $this->mount($app);
  }

  abstract public function mount(KarambolApp $app);

}
