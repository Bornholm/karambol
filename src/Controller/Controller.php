<?php

namespace Karambol\Controller;

use Karambol\KarambolApp;
use Karambol\AccessControl\Resource;
use Karambol\AccessControl\BaseActions;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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

  protected function ifAuthorized(callable $callback) {
    return function() use ($callback) {

      $args = func_get_args();
      $request = $this->get('request');
      $resource = new Resource('url', $request->getRequestURI());

      $authCheck = $this->get('security.authorization_checker');
      $canAccess = $authCheck->isGranted(BaseActions::ACCESS, $resource);

      if(!$canAccess) throw new AccessDeniedException();

      return call_user_func_array($callback, $args);

    };
  }

  abstract public function mount(KarambolApp $app);

}
