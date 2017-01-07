<?php

namespace Karambol\VirtualSet;

class ItemMatcher {

  protected $criteria;

  public function __construct(array $criteria) {
    $this->criteria = $criteria;
  }

  public function matches($item) {
    $item = is_array($item) ? ((object) $item) : $item;
    foreach($this->criteria as $key => $value) {
      if($value !== $this->getPropertyValue($item, $key)) return false;
    }
    return true;
  }

  protected function getPropertyValue($item, $propertyPath) {

    $position = 0;
    $parts = explode('.', $propertyPath);
    $partsLength = count($parts);

    while($position < $partsLength) {

      $key = $parts[$position];

      if(is_array($item) && isset($item[$key])) {
      $item = $item[$parts[$position++]];
        continue;
      }

      if(isset($item->$key)) {
        $item = $item->$parts[$position++];
        continue;
      }

      $getter = [$item, 'get'.ucfirst($key)];
      if(is_callable($getter)) {
        $item = $item->{$getter[1]}();
        $position++;
        continue;
      }

      $tester = [$item, 'has'.ucfirst($key)];
      if(is_callable($tester)) {
        $item = $item->$tester[1]();
        $position++;
        continue;
      }

      return null;

    }

    return $item;

  }

}
