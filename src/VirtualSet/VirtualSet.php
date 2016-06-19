<?php

namespace Karambol\VirtualSet;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Karambol\VirtualSet\ItemCountEvent;
use Karambol\VirtualSet\ItemSearchEvent;

class VirtualSet extends EventDispatcher implements \Countable, \IteratorAggregate {

  protected $position;

  public function findAll(array $criteria = []) {
    return $this->find($criteria);
  }

  public function findOne(array $criteria = []) {
    $results = $this->find($criteria, 1);
    return isset($results[0]) ? $results[0] : null;
  }

  public function find(array $criteria = [], $limit = null) {
    $event = new ItemSearchEvent($criteria, $limit);
    $this->dispatch(ItemSearchEvent::NAME, $event);
    return $event->getResults();
  }

  public function getIterator() {
    $event = new ItemIterateEvent();
    $this->dispatch(ItemIterateEvent::NAME, $event);
    return $event->getIteratorAggregate();
  }

  public function count() {
    $event = new ItemCountEvent();
    $this->dispatch(ItemCountEvent::NAME, $event);
    return $event->getTotal();
  }

}
