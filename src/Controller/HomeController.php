<?php

namespace Karambol\Controller;

use Karambol\KarambolApp;
use Karambol\Page\PageInterface;
use Karambol\AccessControl\BaseActions;

class HomeController extends Controller {

  public function mount(KarambolApp $app) {
    $app->get('/', array($this, 'showHome'))->bind('home');
    $app->get('/home', array($this, 'showDefaultHome'))->bind('default_home');
    $app->get('/p/{pageSlug}', array($this, 'showFramedPage'))->bind('framed_page');
  }

  public function showHome() {
    $homePage = $this->get('pages')->getHomepage();
    if($homePage instanceof PageInterface) {
      return $this->redirect($homePage->getUrl());
    }
    return $this->showDefaultHome();
  }

  public function showDefaultHome() {
    $twig = $this->get('twig');
    return $twig->render('home/index.html.twig');
  }

  public function showFramedPage($pageSlug) {

    $this->assertAccessAuthorization();

    $twig = $this->get('twig');
    $pagesSvc = $this->get('pages');

    $page = $pagesSvc->findOne(['slug' => $pageSlug]);

    if(!$page) return $this->app->abort(404, 'Page not found !');

    $authChecker = $this->get('security.authorization_checker');
    $authChecker->isGranted(BaseActions::READ, $page);

    return $twig->render('home/page.html.twig', [
      'page' => $page
    ]);
  }

}
