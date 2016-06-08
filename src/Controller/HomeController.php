<?php

namespace Karambol\Controller;

use Karambol\KarambolApp;
use Karambol\Page\PageInterface;

class HomeController extends Controller {

  public function mount(KarambolApp $app) {
    $app->get('/', array($this, 'showHome'))->bind('home');
    $app->get('/home', array($this, 'showDefaultHome'))->bind('default-home');
    $app->get('/p/{pageSlug}', array($this, 'showFramedPage'))->bind('framed-page');
  }

  public function showHome() {
    $homePage = $this->get('page')->getHomepage();
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

    $twig = $this->get('twig');
    $pageService = $this->get('page');

    $page = $pageService->findPageBySlug($pageSlug);

    if(!$page) return $this->app->abort(404, 'Page not found !');

    return $twig->render('home/page.html.twig', [
      'page' => $page
    ]);
  }

}
