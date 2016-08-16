<?php

namespace Karambol\Entity;

use Doctrine\ORM\Mapping as ORM;
use Karambol\Account\UserInterface;
use Karambol\AccessControl\ResourceInterface;
use Karambol\AccessControl\ResourceOwnerInterface;

/**
 * @ORM\MappedSuperclass
 */
class BaseUser implements UserInterface {

  /**
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\Column(type="integer")
   */
  protected $id;

  /**
   * @ORM\Column(type="string", length=254, unique=true)
   */
  protected $username;

  /**
   * @ORM\Column(type="text", nullable=true)
   */
  protected $password;

  protected $roles = [];

  public function getId() {
    return $this->id;
  }

  public function setUsername($username) {
    $this->username = $username;
  }

  public function getUsername() {
    return $this->username;
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
    // No plain text credentials to remove
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

  public function owns(ResourceInterface $resource) {
    return $resource->getResourceType() === 'user' && $resource->getResourceId() === $this->getId();
  }

  public function createRuleEngineView() {
    $view = new \stdClass();
    $view->id = $this->getId();
    $view->username = $this->getUsername();
    return $view;
  }

  public function __toString() {
    return sprintf('user#%s', $this->getId());
  }

}
