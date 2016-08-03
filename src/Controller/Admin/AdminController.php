<?php

namespace Karambol\Controller\Admin;

use Karambol\KarambolApp;
use Karambol\Controller\Controller;

class AdminController extends Controller {

  public function mount(KarambolApp $app) {
    $app->get('/admin', $this->ifAllowed([$this, 'showAdminIndex']))->bind('admin');
  }

  public function showAdminIndex() {
    $twig = $this->get('twig');
    return $twig->render('admin/index.html.twig');
  }

}
