<?php

namespace Karambol\Page;

use Karambol\KarambolApp;
use Symfony\Component\EventDispatcher\EventDispatcher;

class PageService extends EventDispatcher {

  protected $app;

  public function __construct(KarambolApp $app) {
    $this->app = $app;
  }

  public function getPages() {
    $event = new PageEvent();
    $this->dispatch(PageEvent::PAGES_LIST, $event);
    return $event->getPages();
  }

  public function findPageBySlug($pageSlug) {
    $pages = $this->getPages();
    foreach($pages as $p) {
      if($p->getSlug() === $pageSlug) return $p;
    }
    return null;
  }

}
