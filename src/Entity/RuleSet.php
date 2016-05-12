<?php

namespace Karambol\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="rulesets")
 */
class RuleSet {

  /**
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\Column(type="integer")
   */
  protected $id;

  /**
   * @ORM\Column(type="text", length=128)
   */
  protected $name;

  /**
   * @ORM\OneToMany(targetEntity="Karambol\Entity\Rule", mappedBy="ruleset", orphanRemoval=true, cascade="all")
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

  public function addRule(Rule $rule) {
    $rule->setRuleset($this);
    $this->rules->add($rule);
    return $this;
  }

  public function removeRule(Rule $rule) {
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
