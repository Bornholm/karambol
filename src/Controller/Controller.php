<?php

namespace Karambol\Controller;

use Karambol\KarambolApp;

abstract class Controller implements ControllerInterface {

  protected $app;

  /**
   * Get an application service by its name
   *
   * @param string $service The name of the wanted service
   * @return mixed The application service, null if not found
   */
  public function get($service) {
    return isset($this->app[$service]) ? $this->app[$service] : null;
  }

  /**
   * Return a HTTP redirect response to the provided URL
   *
   * @param string $url The URL to redirect to
   * @param integer $status The HTTP status of the response, default "302"
   * @return Response The HTTP Response
   */
  public function redirect($url, $status = 302) {
    return $this->app->redirect($url, $status);
  }

  /**
   * Return an aborted HTTP response with the provided status and message
   *
   * @param integer $status The HTTP status to return
   * @param integer $status The message to attach to the response
   * @return Response The HTTP Response
   */
  public function abort($status, $message = '') {
    return $this->app->abort($status, $message);
  }

  /**
   * Attach controler to the provided application
   *
   * @param KarambolApp $app To app to attach the controller to
   * @return static The controller
   */
  public function bindTo(KarambolApp $app) {
    $this->app = $app;
    $this->mount($app);
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
