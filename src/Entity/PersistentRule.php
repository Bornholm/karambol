<?php

namespace Karambol\Entity;

use Doctrine\ORM\Mapping as ORM;
use Karambol\RuleEngine\Rule\RuleInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="rules")
 * @ORM\HasLifecycleCallbacks
 */
class PersistentRule implements RuleInterface {

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

  /**
   * @ORM\Column(name="internal_rule", type="text", nullable=false)
   */
  protected $internalRuleClass;

  /**
   * @ORM\Column(name="internal_rule_options", type="json_array")
   */
  protected $internalRuleOptions;

  protected $internalRule;

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

  public function setInternalRule(RuleInterface $rule) {
    $this->internalRule = $rule;
    return $this;
  }

  public function getInternalRule() {
    return $this->internalRule;
  }

  /**
   * @ORM\PostLoad()
   */
  public function loadInternalRule() {
    $internalRuleClass = $this->internalRuleClass;
    $internalRule = new $internalRuleClass();
    $rule->setOptions($this->internalRuleOptions);
    $this->setInternalRule($internalRule);
  }

  /**
   * @ORM\PreUpdate()
   * @ORM\PrePersist()
   */
  public function prepareToSave() {
    $internalRule = $this->getInternalRule();
    $this->internalRuleClass = get_class($internalRule);
    $this->internalRuleOptions = $internalRule->getOptions();
  }

  public function getOptions() {
    return $this->getInternalRule()->getOptions();
  }

  public function setOptions(array $options) {
    $this->getInternalRule()->setOptions($options);
    return $this;
  }

  public function test($subject) {
    return $this->getInternalRule()->test($subject);
  }

}
