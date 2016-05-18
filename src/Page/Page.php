<?php

namespace Karambol\Page;

use Cocur\Slugify\Slugify;
use Karambol\Page\PageInterface;

class Page implements PageInterface {

  protected $url;
  protected $slug;
  protected $label;

  public function __construct($label, $url) {
    $this->label = $label;
    $this->url = $url;
    $this->updateSlug();
  }

  public function getLabel() {
    return $this->label;
  }

  public function setLabel($label) {
    $this->label = $label;
    $this->updateSlug();
    return $this;
  }

  public function getUrl() {
    return $this->url;
  }

  public function setUrl($url) {
    $this->url = $url;
    return $this;
  }

  public function getSlug() {
    return $this->slug;
  }

  protected function updateSlug() {
    $slugify = new Slugify();
    $this->slug = $slugify->slugify($this->label);
  }

}
