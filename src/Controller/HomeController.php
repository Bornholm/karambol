<?php

namespace Karambol\Controller;

use Karambol\KarambolApp;

class HomeController extends Controller {

  public function mount(KarambolApp $app) {
    $app->get('/', array($this, 'showHome'))->bind('homepage');
  }

  public function showHome() {
    $twig = $this->get('twig');
    return $twig->render('home/index.html.twig', [
      'test' => 'hello world !'
    ]);
  }

}
