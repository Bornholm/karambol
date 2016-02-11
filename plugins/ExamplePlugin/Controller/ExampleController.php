<?php

namespace ExamplePlugin\Controller;

use Karambol\Controller\Controller;
use Karambol\KarambolApp;

class ExampleController extends Controller {

  public function showExample() {
    $twig = $this->get('twig');
    return $twig->render('example-plugin.html.twig');
  }

  protected function _mount(KarambolApp $app) {
    $app->get('/example-plugin', array($this, 'showExample'));
  }

}
