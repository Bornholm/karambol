<?php

namespace Karambol\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Karambol\Entity\UserAttribute;

/**
 * @ORM\Entity
 * @ORM\Table(name="rulesets")
 * @ORM\HasLifecycleCallbacks
 */
class PersistentRuleSet {

  /**
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\Column(type="integer")
   */
  protected $id;

  /**
   * @ORM\OneToMany(targetEntity="Karambol\Entity\PersistentRule", mappedBy="ruleset", orphanRemoval=true, cascade="all")
   */
  protected $rules;

  public function __construct() {
    $this->rules = new ArrayCollection();
  }

  public function getId() {
    return $this->id;
  }

  public function addRule(PersistentRule $attribute) {
    $attribute->setRuleSet($this);
    $this->rules->add($attribute);
    return $this;
  }

  public function removeRule(PersistentRule $attribute) {
    $attribute->setRuleSet(null);
    $this->rules->removeElement($attribute);
    return $this;
  }

  public function getRules() {
    return $this->rules;
  }

  public function setRules(ArrayCollection $rules) {
    $this->rules = $rules;
    return $this;
  }

}
