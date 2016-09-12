<?php

namespace Karambol\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="users_extensions", uniqueConstraints={
 *  @ORM\UniqueConstraint(name="user_unique_extension", columns={"user", "name"})
 * })
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 */
class UserExtension {

  /**
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\Column(type="integer")
   */
  protected $id;

  /**
   * @ORM\Column(type="string", length=64, nullable=false)
   */
  protected $name;

  /**
   * @ORM\ManyToOne(targetEntity="Karambol\Entity\User", inversedBy="extensions")
   * @ORM\JoinColumn(name="user", referencedColumnName="id", onDelete="CASCADE", nullable=false)
   */
  protected $user;

  public function getId() {
    return $this->id;
  }

  /**
   * @return
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @param  $name
   *
   * @return static
   */
  public function setName($name)
  {
    $this->name = $name;
    return $this;
  }

  /**
   * @return User
   */
  public function getUser()
  {
    return $this->user;
  }

  /**
   * @param User  $user
   *
   * @return static
   */
  public function setUser(User $user = null)
  {
    $this->user = $user;
    return $this;
  }
}
