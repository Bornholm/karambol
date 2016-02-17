<?php

namespace Karambol\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="users_attributes", uniqueConstraints={
 *  @ORM\UniqueConstraint(name="user_attribute_name_idx", columns={"user_id", "name"})
 * })
 */
class UserAttribute {

  /**
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\Column(type="integer")
   */
  protected $id;

  /**
   * @ORM\Column(type="string")
   */
  protected $name;

  /**
   * @ORM\Column(type="json_array")
   */
  protected $value;

  /**
   * @ORM\ManyToOne(targetEntity="Karambol\Entity\User", inversedBy="attributes")
   * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
   */
  protected $user;

  public function getId() {
    return $this->id;
  }

  public function setName($name) {
    $this->name = $name;
    return $this;
  }

  public function getName() {
    return $this->name;
  }

  public function setValue($value) {
    if( !is_array($value) ) $value = ['$_v' => $value];
    $this->value = $value;
  }

  public function getValue() {
    $raw = $this->value;
    return isset($raw['$_v']) ? $raw['$_v'] : $raw;
  }

  public function setUser($user = null) {
    $this->user = $user;
    return $this;
  }

  public function getUser() {
    return $this->user;
  }


}
