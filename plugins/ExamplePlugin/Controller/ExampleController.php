<?php

namespace ExamplePlugin\Controller;

use Karambol\Controller\Controller;

class ExampleController extends Controller {

  public function showExample() {
    $twig = $this->get('twig');
    return $twig->render('example-plugin.html.twig');
  }

  public function mount() {
    $app = $this->getApp();
    $app->get('/example-plugin', array($this, 'showExample'));
  }

}