<?php

namespace Karambol\Controller;

use Karambol\KarambolApp;

class AuthenticationController extends Controller {

  public function mount(KarambolApp $app) {
    $app->get('/login', array($this, 'showLogin'))->bind('login');
  }

  public function showLogin() {
    $twig = $this->get('twig');
    $lastError = $this->get('security.last_error');
    return $twig->render('auth/login.html.twig', [
      'error'         => $lastError($this->get('request')),
      'lastUsername' => $this->get('session')->get('_security.last_username')
    ]);
  }

}
