<?php

namespace Karambol\VirtualSet;

use Symfony\Component\EventDispatcher\Event;

class ItemSearchEvent extends Event {

  const NAME = 'virtualset.search';

  protected $criteria;
  protected $matcher;
  protected $results = [];
  protected $limit;

  public function __construct(array $criteria = [], $limit = null) {
    $this->criteria = $criteria;
    $this->limit = $limit;
    $this->matcher = new ItemMatcher($criteria);
  }

  public function addItems(array $items) {
    foreach($items as $item) {
      $this->addItem($item);
    }
    return $this;
  }

  public function addItem($item) {
    if($this->isLimitReached()) return $this;
    if($this->getResultIndex($item) === false && $this->matchesCriteria($item))  {
      $this->results[] = $item;
      if($this->isLimitReached()) $this->stopPropagation();
    }
    return $this;
  }

  public function removeItem($item) {
    if($this->isLimitReached()) return $this;
    $itemIndex = $this->getItemIndex($item);
    if($itemIndex !== false) array_splice($this->results, $itemIndex);
    return $this;
  }

  public function hasItem($item) {
    return $this->getItemIndex($item) !== false;
  }

  public function matchesCriteria($item) {
    return $this->matcher->matches($item);
  }

  public function getCriteria() {
    return $this->criteria;
  }

  public function getResults() {
    return $this->results;
  }

  protected function isLimitReached() {
    return $this->limit !== null && count($this->results) >= $this->limit;
  }

  protected function getResultIndex($item) {
    return array_search($item, $this->results, true);
  }

}
