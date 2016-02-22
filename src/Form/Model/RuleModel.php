<?php

namespace Karambol\Form\Model;

use Karambol\Entity\PersistentRule;
use Doctrine\Common\Collections\ArrayCollection;

class RuleModel {

  public static function fromPersistentRule(PersistentRule $ruleset) {
    $model = new RuleModel();
    return $model;
  }

  public $comparator;
  public $criteria;
  public $propertyPath;

}
