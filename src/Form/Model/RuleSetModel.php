<?php

namespace Karambol\Form\Model;

use Karambol\Entity\PersistentRuleSet;
use Doctrine\Common\Collections\ArrayCollection;

class RuleSetModel {

  public static function fromPersistentRuleSet(PersistentRuleSet $ruleset) {
    $model = new RuleSetModel();
    $model->rules = new ArrayCollection();
    $model->label = $ruleset->getLabel();
    return $model;
  }

  public $label;
  public $rules;

}
