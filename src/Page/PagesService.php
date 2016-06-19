<?php

namespace Karambol\Page;

use Karambol\KarambolApp;
use Karambol\VirtualSet\VirtualSet;

class PagesService extends VirtualSet {

  protected $app;
  protected $homepage;

  public function __construct(KarambolApp $app) {
    $this->app = $app;
  }

  public function getHomepage() {
    return $this->homepage;
  }

  public function setHomepage(PageInterface $homepage) {
    $this->homepage = $homepage;
  }

}
