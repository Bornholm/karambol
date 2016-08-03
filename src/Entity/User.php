<?php

namespace Karambol\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Karambol\Entity\UserAttribute;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 * @ORM\HasLifecycleCallbacks
 */
class User extends BaseUser {

  /**
   * @ORM\OneToMany(targetEntity="Karambol\Entity\UserAttribute", mappedBy="user", orphanRemoval=true, cascade="all")
   */
  protected $attributes;

  public function __construct() {
    $this->attributes = new ArrayCollection();
  }

  public function getEmail() {
    return $this->getUsername();
  }

  public function setEmail($email) {
    return $this->setUsername($email);
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

  public function addAttribute(UserAttribute $attribute) {
    $attribute->setUser($this);
    $this->attributes->add($attribute);
    return $this;
  }

  public function removeAttribute(UserAttribute $attribute) {
    $attribute->setUser(null);
    $this->attributes->removeElement($attribute);
    return $this;
  }

  public function getAttributes() {
    return $this->attributes;
  }

  public function setAttributes(ArrayCollection $attributes) {
    $this->attributes = $attributes;
    return $this;
  }

  public function getAttrs() {
    $attrs = [];
    foreach($this->attributes as $attr) {
      $attrs[$attr->getName()] = $attr->getValue();
    }
    return $attrs;
  }

  protected function findAttributeByName($attrName) {
    foreach($this->attributes as $attr) {
      if($attr->getName() === $attrName) {
        return $attr;
      }
    }
  }

}
