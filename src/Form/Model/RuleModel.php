<?php

namespace Karambol\Form\Model;

use Karambol\Entity\PersistentRuleSet;
use Doctrine\Common\Collections\ArrayCollection;

class RuleModel {

  public static function fromPersistentRule(PersistentRule $ruleset) {
    return $model;
  }

  public $comparator;
  public $criteria;
  public $propertyPath;

}
