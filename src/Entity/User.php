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
class User implements UserInterface {

  /**
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\Column(type="integer")
   */
  protected $id;

  /**
   * @ORM\Column(type="string", length=128, unique=true)
   */
  protected $username;

  /**
   * @ORM\Column(type="text")
   */
  protected $password;

  /**
   * @ORM\Column(type="text")
   */
  protected $salt;

  /**
   * @ORM\OneToMany(targetEntity="Karambol\Entity\UserAttribute", mappedBy="user", orphanRemoval=true, cascade="all")
   */
  protected $attributes;

  protected $roles = [];

  public function __construct() {
    $this->attributes = new ArrayCollection();
  }

  public function getId() {
    return $this->id;
  }

  public function getUsername() {

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

  public function getPassword() {

  }

  public function getSalt() {

  }

  public function eraseCredentials() {

  }

  public function getRoles() {
    return $this->roles;
  }

  public function addRole($role) {
    if(!in_array($role, $this->roles)) $this->roles[] = $role;
    return $this;
  }

  public function removeRole($role) {
    $roleIndex = array_search($role, $this->roles);
    if($roleIndex !== false) array_splice($this->roles, $roleIndex, 1);
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
