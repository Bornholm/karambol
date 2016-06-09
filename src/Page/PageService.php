<?php

namespace Karambol\Page;

use Karambol\KarambolApp;
use Symfony\Component\EventDispatcher\EventDispatcher;

class PageService extends EventDispatcher {

  protected $app;
  protected $homepage;

  public function __construct(KarambolApp $app) {
    $this->app = $app;
  }

  public function getSystemPages() {
    $event = new PageEvent();
    $this->dispatch(PageEvent::LIST_SYSTEM_PAGES, $event);
    return $event->getPages();
  }

  public function findPageBySlug($pageSlug) {

    $systemPages = $this->getSystemPages();

    foreach($pages as $p) {
      if($p->getSlug() === $pageSlug) return $p;
    }

    return $this->app['orm']
      ->getRepository('Karambol\Entity\CustomPage')
      ->findOneBySlug($pageSlug)
    ;

  }

  public function getHomepage() {
    return $this->homepage;
  }

  public function setHomepage(PageInterface $homepage) {
    $this->homepage = $homepage;
  }

}
