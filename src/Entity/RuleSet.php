<?php

namespace Karambol\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="rulesets")
 */
class RuleSet {

  const PERSONALIZATION = 'personalization';
  const ROLES = 'roles';

  /**
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\Column(type="integer")
   */
  protected $id;

  /**
   * @ORM\Column(type="string", length=128, unique=true)
   */
  protected $name;

  /**
   * @ORM\OneToMany(targetEntity="Karambol\Entity\CustomRule", mappedBy="ruleset", orphanRemoval=true, cascade="all")
   */
  protected $rules;


  public function __construct() {
    $this->rules = new ArrayCollection();
  }

  public function getId() {
    return $this->id;
  }

  public function getRules() {
    return $this->rules;
  }

  public function setRules(ArrayCollection $rules) {
    $this->rules = $rules;
    return $this;
  }

  public function addRule(CustomRule $rule) {
    $rule->setRuleset($this);
    $this->rules->add($rule);
    return $this;
  }

  public function removeRule(CustomRule $rule) {
    $rule->setRuleset(null);
    $this->rules->removeElement($rule);
    return $this;
  }

  public function getName() {
    return $this->name;
  }

  public function setName($name) {
    $this->name = $name;
    return $this;
  }

}
