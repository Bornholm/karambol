<?php

namespace Karambol\VirtualSet;

use Symfony\Component\PropertyAccess\PropertyAccess;

class ItemMatcher {

  protected $propertyAccessor;
  protected $criteria;

  public function __construct(array $criteria) {
    $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    $this->criteria = $criteria;
  }

  public function matches($item) {
    $isArray = is_array($item);
    $accessor = $this->propertyAccessor;
    foreach($this->criteria as $key => $value) {
      $propertyPath = $isArray ? '['.$key.']' : $key;
      if($value !== $accessor->getValue($item, $propertyPath)) return false;
    }
    return true;
  }

}
