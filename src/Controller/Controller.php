<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Karambol\Controller;

use Karambol\KarambolApp;

abstract class Controller implements ControllerInterface {
  
  /**
   * Application
   * @var KarambolApp 
   */
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

  /**
   * Add a Flash message to the response
   *
   * @param string $content The message
   * @param array $data The associated metadata of the message
   * @return static $this
   */
  protected function addFlashMessage($content, $options = []) {
    $session = $this->get('session');
    $session->getFlashBag()->add('message', [
      'content' => $content,
      'options' => $options
    ]);
    return $this;
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
   * Definition des routes
   * @param KarambolApp $app Application
   * @author William Petit
   */
  abstract public function mount(KarambolApp $app);

}
