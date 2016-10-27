<?php

namespace Karambol\Page;

use Cocur\Slugify\Slugify;
use Karambol\Page\PageInterface;
use Karambol\AccessControl\ResourceInterface;

class Page implements PageInterface, ResourceInterface {

  protected $url;
  protected $slug;
  protected $label;

  public function __construct($label, $url, $slug) {
    $this->label = $label;
    $this->url = $url;
    $this->slug = $slug;
  }

  public function getLabel() {
    return $this->label;
  }

  public function setLabel($label) {
    $this->label = $label;
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

  public function setSlug($slug) {
    $this->slug = $slug;
    return $this;
  }

  public function getResourceType() {
    return 'page';
  }

  public function getResourceId() {
    return $this->getSlug();
  }

  public function getResourceProperty() {
    return null;
  }

  public function getResourceOwnerId() {
    return null;
  }

}
