<?php

namespace Karambol\Page;

use Symfony\Component\EventDispatcher\Event;

class PageEvent extends Event {

  const PAGES_LIST = 'pages.list';

  protected $pages = [];

  public function getPages() {
    return $this->pages;
  }

  public function addPage(PageInterface $page) {
    $this->pages[] = $page;
  }

  public function removePage(PageInterface $page) {
    if(!in_array($page)) return $this;
    array_splice($this->pages, array_search($page, $this->pages));
    return $this;
  }

  public function getPageBySlug($slug) {
    foreach($this->pages as $page) {
      if($page->getSlug() === $slug) return $page;
    }
    return null;
  }

}
