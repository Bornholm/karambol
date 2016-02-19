<?php

namespace Karambol\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="rules")
 */
class PersistentRule {

  /**
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\Column(type="integer")
   */
  protected $id;

  /**
   * @ORM\ManyToOne(targetEntity="Karambol\Entity\PersistentRuleSet", inversedBy="rules")
   * @ORM\JoinColumn(name="ruleset_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
   */
  protected $ruleSet;

  public function getId() {
    return $this->id;
  }

  public function setRuleSet($ruleSet = null) {
    $this->ruleSet = $ruleSet;
    return $this;
  }

  public function getRuleSet() {
    return $this->ruleSet;
  }


}
