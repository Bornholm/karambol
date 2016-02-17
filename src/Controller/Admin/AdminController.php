<?php

namespace Karambol\Controller\Admin;

use Karambol\KarambolApp;
use Karambol\Controller\Controller;

class AdminController extends Controller {

  public function showAdminIndex() {
    $twig = $this->get('twig');
    return $twig->render('admin/index.html.twig');
  }

  public function mount(KarambolApp $app) {
    $app->get('/admin', array($this, 'showAdminIndex'))->bind('admin');
  }

}
