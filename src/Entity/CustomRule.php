<?php

namespace Karambol\Entity;

use Doctrine\ORM\Mapping as ORM;
use Karambol\RuleEngine\RuleInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="rules")
 * @ORM\HasLifecycleCallbacks
 */
class CustomRule implements RuleInterface {

  /**
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\Column(type="integer")
   */
  protected $id;

  /**
   * @ORM\Column(name="`condition`", type="text", nullable=false)
   */
  protected $condition;

  /**
   * @ORM\Column(type="text", nullable=false)
   */
  protected $action;

  /**
   * @ORM\ManyToOne(targetEntity="Karambol\Entity\RuleSet", inversedBy="rules")
   * @ORM\JoinColumn(name="ruleset", referencedColumnName="id", onDelete="CASCADE", nullable=false)
   */
  protected $ruleset;

  public function getId() {
    return $this->id;
  }

  public function getCondition() {
    return $this->condition;
  }

  public function setCondition($condition) {
    $this->condition = $condition;
  }

  public function getAction() {
    return $this->action;
  }

  public function setAction($action) {
    $this->action = $action;
  }

  public function getActions() {
    return preg_split('/(\n)+|(\r\n)+/', $this->action);
  }

  public function getRuleset() {
    return $this->ruleset;
  }

  public function setRuleset($ruleset = null) {
    $this->ruleset = $ruleset;
  }

}
