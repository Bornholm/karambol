<?php

namespace Karambol\Entity;

use Doctrine\ORM\Mapping as ORM;
use Karambol\RuleEngine\Context\ProtectableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Karambol\AccessControl\ResourceInterface;
use Karambol\AccessControl\ResourceOwnerInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 * @ORM\HasLifecycleCallbacks
 */
class User implements UserInterface, ResourceOwnerInterface, ProtectableInterface {

  const RESOURCE_TYPE = 'user';

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
   * @ORM\Column(type="string", length=254, unique=true, nullable=true)
   */
  protected $email;

  /**
   * @ORM\Column(type="text", nullable=true)
   */
  protected $password;

  /**
   * @ORM\Column(type="string", length=64, nullable=true)
   */
  protected $passwordToken;

  /**
   * @ORM\Column(type="datetime", nullable=true)
   */
  protected $passwordTokenTimestamp;

  /**
  * @var ArrayCollection
   * @ORM\OneToMany(targetEntity="Karambol\Entity\UserExtension", mappedBy="user", orphanRemoval=true, cascade="all")
   */
  protected $extensions;

  protected $roles = [];

  public function __construct() {
    $this->extensions = new ArrayCollection();
  }

  public function getId() {
    return $this->id;
  }

  public function setUsername($username) {
    $this->username = $username;
  }

  public function getUsername() {
    return $this->username;
  }

  public function getEmail() {
    return $this->email;
  }

  public function setEmail($email) {
    $this->email = $email;
    return $this;
  }

  /**
   * Add an extension to the user's collection
   *
   * @param UserExtension $extension
   * @return static
   */
  public function addExtension(UserExtension $extension) {
    $extension->setUser($this);
    $this->extensions->add($extension);
    return $this;
  }

  /**
   * Remove an extension from the user's collection
   *
   * @param UserExtension $extension
   * @return static
   */
  public function removeExtension(UserExtension $extension) {
    $extension->setUser(null);
    $this->extensions->removeElement($extension);
    return $this;
  }

  /**
   * Get the user's extensions
   *
   * @return ArrayCollection The user's extensions
   */
  public function getExtensions() {
    return $this->extensions;
  }

  /**
   * Get a user's extension by its name
   *
   * @param string $extensionName The extension's name
   * @param class $defaultExtensionClass If the extension does not exists, automatically create and attach an new instance based on this class
   * @return UserExtension The user extension
   */
  public function getExtensionByName($extensionName, $defaultExtensionClass = null) {

    foreach($this->getExtensions() as $extension) {
      if($extension->getName() === $extensionName) return $extension;
    }

    if($defaultExtensionClass === null) return null;

    if( !((new $defaultExtensionClass) instanceof UserExtension) )
      throw new \Exception(sprintf('The extension "%s" must extends Karambol\Entity\UserExtension !', $defaultExtensionClass));

    $extension = new $defaultExtensionClass();
    $extension->setName($extensionName);
    $this->addExtension($extension);

    return $extension;

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

  public function resetPasswordToken() {
    $this->passwordToken = bin2hex(random_bytes(32));
    $this->passwordTokenTimestamp = new \DateTime();
  }

  public function clearPasswordToken() {
    $this->passwordToken = null;
    $this->passwordTokenTimestamp = null;
  }

  public function getPasswordToken() {
    return $this->passwordToken;
  }

  public function getPasswordTokenTimestamp() {
    return $this->passwordTokenTimestamp;
  }

  public function owns(ResourceInterface $resource) {

    $owns = $resource->getResourceType() === self::RESOURCE_TYPE &&
      $resource->getResourceId() === $this->getId()
    ;

    if($owns) return true;

    foreach($this->getExtensions() as $extension) {
      if($extension instanceof ResourceOwnerInterface) {
        $owns = $extension->owns($resource);
        if($owns) return true;
      }
    }

    return false;

  }

  public function getExposedAttributes() {
    return [
      'id' => $this->getId(),
      'username' => $this->getUsername()
    ];
  }

  public function __toString() {
    return sprintf('user[%s]', $this->getId());
  }

}
