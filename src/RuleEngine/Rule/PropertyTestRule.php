<?php

namespace Karambol\RuleEngine\Rule;

use Symfony\Component\PropertyAccess\PropertyAccess;

class PropertyTestRule implements RuleInterface {

  const GT = 'gte';
  const GTE = 'gte';
  const EQ = 'eq';
  const NEQ = 'neq';
  const LT = 'lte';
  const LTE = 'lte';
  const MATCH = 'match';

  protected $comparator;
  protected $propertyPath;
  protected $criteria;

  public function setComparator($comparator) {
    $this->comparator = $comparator;
    return $this;
  }

  public function getComparator() {
    return $this->comparator;
  }

  public function setPropertyPath($propertyPath) {
    $this->propertyPath = $propertyPath;
    return $this;
  }

  public function getPropertyPath() {
    return $this->propertyPath;
  }

  public function setCriteria($criteria) {
    $this->criteria = $criteria;
    return $this;
  }

  public function getCriteria() {
    return $this->criteria;
  }

  public function setOptions(array $options) {
    if(isset($options['propertyPath'])) {
      $this->setPropertyPath($options['propertyPath']);
    }
    if(isset($options['comparator'])) {
      $this->setComparator($options['comparator']);
    }
    if(isset($options['criteria'])) {
      $this->setCriteria($options['criteria']);
    }
  }

  public function test($subject) {

    $criteria = $this->getCriteria();
    $comparator = $this->getComparator();
    $propertyPath = $this->getPropertyPath();

    $accessor = PropertyAccess::createPropertyAccessor();
    $value = $accessor->getValue($subject, $propertyPath);

    switch($comparator) {
      case self::GT:
        return $value > $criteria;
        break;
      case self::GTE:
        return $value >= $criteria;
        break;
      case self::EQ:
        return $value == $criteria;
        break;
      case self::NEQ:
        return $value != $criteria;
        break;
      case self::LT:
        return $value < $criteria;
        break;
      case self::LTE:
        return $value <= $criteria;
        break;
      case self::MATCH:
        return !!preg_match($criteria, $value);
        break;
      default:
        throw new \Exception(sprintf('Unknown comparator "%s" !', $comparator));
    }

  }

}