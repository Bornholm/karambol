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

  protected function assertUrlAccessAuthorization($throwsException = true) {

    $request = $this->get('request');
    $resource = new Resource('url', $request->getRequestURI());

    $authCheck = $this->get('security.authorization_checker');
    $canAccess = $authCheck->isGranted(BaseActions::ACCESS, $resource);

    if(!$canAccess) {
      if($throwsException) throw new AccessDeniedException();
      return false;
    }

    return true;

  }

  /**
   * Render a Twig template with the provided data
   *
   * @param string $templatePath The template's path relative to the views base directory
   * @param array $data The data to inject in the template
   * @return The rendered template
   */
  protected function render($templatePath, $data = []) {
    $twig = $this->get('twig');
    return $twig->render($templatePath, $data);
  }

  abstract public function mount(KarambolApp $app);

}
