<?php

namespace DashboardPlugin\Controller;

use Karambol\Controller\Controller;
use Karambol\KarambolApp;

class DashboardController extends Controller {

  public function mount(KarambolApp $app) {
    $app->get('/dashboard', [$this, 'showDashboard'])->bind('dashboard');
  }

  public function showDashboard() {
    $twig = $this->get('twig');
    return $twig->render('dashboard/index.html.twig', [
      'widgets' => [
        [ ['label' => 'Test', 'columnOffset' => null, 'columnWidth' => 6, 'order' => 0, 'url' => 'http://linuxfr.org' ] ]
      ]
    ]);
  }

}
