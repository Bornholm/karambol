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
   * @ORM\Column(type="string", length=254, unique=true)
   */
  protected $email;

  /**
   * @ORM\Column(type="text")
   */
  protected $password;

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

  public function getEmail() {
    return $this->email;
  }

  public function setEmail($email) {
    $this->email = $email;
    return $this;
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

  public function getUsername() {
    return $this->getEmail();
  }

  public function changePassword($hash, $salt) {
    $this->password = base64_encode($salt).':'.base64_encode($hash);
  }

  public function getPassword() {
    return base64_decode($this->getPasswordPart(1));
  }

  public function getSalt() {
    return base64_decode($this->getPasswordPart(0));
  }

  protected function getPasswordPart($partIndex) {
    $parts = explode(':', $this->password);
    return count($parts) >= $partIndex+1 ? $parts[$partIndex] : null;
  }

  public function eraseCredentials() {
    $this->password = null;
    return $this;
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
