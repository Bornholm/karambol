<?php

namespace Karambol\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Karambol\Entity\UserAttribute;
use Karambol\RuleEngine\Rule\RuleSet;

/**
 * @ORM\Entity
 * @ORM\Table(name="rulesets")
 * @ORM\HasLifecycleCallbacks
 */
class PersistentRuleSet extends RuleSet {

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

  /**
   * @ORM\Column(type="integer", nullable=false)
   */
  protected $operator;

  /**
   * @ORM\Column(type="text", nullable=false)
   */
  protected $label;

  public function getId() {
    return $this->id;
  }

  public function getLabel() {
    return $this->label;
  }

  public function setLabel($label) {
    $this->label = $label;
    return $this;
  }

}
