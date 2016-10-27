<?php

namespace Karambol\Entity;

use Doctrine\ORM\Mapping as ORM;
use Cocur\Slugify\Slugify;
use Karambol\Page\PageInterface;
use Karambol\AccessControl\ResourceInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="custom_pages")
 */
class CustomPage implements PageInterface, ResourceInterface {

  /**
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\Column(type="integer")
   */
  protected $id;

  /**
   * @ORM\Column(type="text", nullable=false)
   */
  protected $url;

  /**
   * @ORM\Column(type="text", nullable=false)
   */
  protected $slug;

  /**
   * @ORM\Column(type="text", nullable=false)
   */
  protected $label;

  public function getId() {
    return $this->id;
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
