<?php

namespace Karambol\Controller;

use Karambol\KarambolApp;

class AuthenticationController extends Controller {

  public function mount(KarambolApp $app) {
    $app->get('/login', [$this, 'showLogin'])->bind('login');
  }

  public function showLogin() {
    $lastError = $this->get('security.last_error');
    $lastError = $lastError($this->get('request'));
    if($lastError) $this->addFlashMessage('auth.'.$lastError, ['type' => 'error']);
    return $this->render('auth/login.html.twig', [
      'lastUsername' => $this->get('session')->get('_security.last_username')
    ]);
  }

}
