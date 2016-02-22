<?php

namespace Karambol\Form\Model;

use Karambol\Entity\PersistentRuleSet;
use Karambol\Entity\PersistentRule;
use Doctrine\Common\Collections\ArrayCollection;

class RuleSetModel {

  public static function fromPersistentRuleSet(PersistentRuleSet $ruleset) {

    $model = new RuleSetModel();
    $rules = $model->rules = new ArrayCollection();

    foreach($ruleset->getRules() as $rule) {
      $rules->add(RuleModel::fromPersistentRule($rule));
    }

    $model->label = $ruleset->getLabel();
    
    return $model;

  }

  public $label;
  public $rules;

}
