<?php

namespace Karambol\Page;

use Cocur\Slugify\Slugify;
use Karambol\Page\PageInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="custom_pages")
 */
class Page implements PageInterface {

  protected $url;
  protected $slug;
  protected $label;
  protected $editable;

  public function __construct($label, $url, $editable = false) {
    $this->label = $label;
    $this->url = $url;
    $this->editable = $editable;
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

  public function isEditable() {
    return $this->editable;
  }

  protected function updateSlug() {
    $slugify = new Slugify();
    $this->slug = $slugify->slugify($this->label);
  }

}
