<?php

namespace Karambol\Page;

use Karambol\KarambolApp;
use Karambol\Page\Page;
use Karambol\Page\PageEvent;

class DefaultPageListener {

  protected $app;

  public function __construct(KarambolApp $app) {
    $this->app = $app;
  }

  public function onPageList(PageEvent $event) {

    $translator = $this->app['translator'];
    $urlGen = $this->app['url_generator'];

    $homePage = new Page($translator->trans('pages.home'), $urlGen->generate('home'), 'home');
    $adminPage = new Page($translator->trans('pages.admin'), $urlGen->generate('admin'), 'admin');
    $loginPage = new Page($translator->trans('pages.login'), $urlGen->generate('login'), 'login');
    $logoutPage = new Page($translator->trans('pages.logout'), $urlGen->generate('logout'), 'logout');

    $linuxfrPage = new Page($translator->trans('Linux-fr'), 'https://linuxfr.org', 'linux-fr');

    $event
      ->addPage($homePage)
      ->addPage($adminPage)
      ->addPage($loginPage)
      ->addPage($logoutPage)
      ->addPAge($linuxfrPage)
    ;

  }

}
