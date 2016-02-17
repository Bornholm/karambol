<?php

namespace Karambol\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Karambol\Entity\UserAttribute;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User {

  /**
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\Column(type="integer")
   */
  protected $id;

  /**
   * @ORM\OneToMany(targetEntity="Karambol\Entity\UserAttribute", mappedBy="user", orphanRemoval=true, cascade="all")
   */
  protected $attributes;

  public function __construct() {
    $this->attributes = new ArrayCollection();
  }

  public function getId() {
    return $this->id;
  }

  public function set($attrName, $attrValue) {
    $attribute = $this->findAttributeByName($attrName);
    if(!$attribute) {
      $attribute = new UserAttribute();
      $attribute->setName($attrName);
      $this->addAttribute($attribute);
    }
    $attribute->setValue($attrValue);
    return $this;
  }

  public function get($attrName, $defaultValue = null) {
    $attribute = $this->findAttributeByName($attrName);
    return $attribute ? $attribute->getValue() : $defaultValue;
  }

  public function addAttribute($attribute) {
    $attribute->setUser($this);
    $this->attributes->add($attribute);
    return $this;
  }

  public function removeAttribute($attribute) {
    $attribute->setUser(null);
    $this->attributes->remove($attribute);
    return $this;
  }

  public function getAttributes() {
    return $this->attributes;
  }

  public function setAttributes($attributes) {
    $this->attributes = $attributes;
    return $this;
  }

  protected function findAttributeByName($attrName) {
    foreach($this->attributes as $attr) {
      if($attr->getName() === $attrName) {
        return $attr;
      }
    }
  }

}
