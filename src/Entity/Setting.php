<?php

namespace Karambol\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="settings")
 */
class Setting {

  /**
   * @ORM\Id
   * @ORM\Column(type="string", length=128, unique=true)
   */
  protected $name;

  /**
   * @ORM\Column(type="json_array")
   */
  protected $value;

  public function getName() {
    return $this->name;
  }

  public function setName($name) {
    $this->name = $name;
    return $this;
  }

  public function getValue() {
    return isset($this->value['v']) ? $this->value['v'] : null;
  }

  public function setValue($value) {
    $this->value = ['v' => $value];
    return $this;
  }

}
