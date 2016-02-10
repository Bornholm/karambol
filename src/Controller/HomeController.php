<?php

namespace Karambol\Controller;

class HomeController extends Controller {

  public function showHome() {
    $twig = $this->get('twig');
    return $twig->render('home/index.html.twig', [
      'test' => 'hello world !'
    ]);
  }

  public function mount() {
    $app = $this->getApp();
    $app->get('/', array($this, 'showHome'));
  }

}