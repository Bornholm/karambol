<?php

namespace Karambol\VirtualSet;


class QueuedIterator implements \Iterator {

  private $position = 0;
  private $currentIteratorIndex = 0;
  private $iterators = [];

  public function append(\Iterator $iterator) {
    $this->iterators[] = $iterator;
  }

  public function rewind() {
    $this->currentIteratorIndex = $this->position = 0;
    foreach($this->iterators as $it) $it->rewind();
  }

  public function current() {
    $currentIterator = $this->getCurrentIterator();
    return $currentIterator->current();
  }

  public function key() {
    return $this->position;
  }

  public function next() {
    $currentIterator = $this->getCurrentIterator();
    ++$this->position;
    return $currentIterator->next();
  }

  public function valid() {
    $currentIterator = $this->getCurrentIterator();
    $valid  = $currentIterator && $currentIterator->valid();
    if(!$valid) {
      $nextIterator = $this->getNextIterator();
      if($nextIterator)  {
        $this->currentIteratorIndex++;
        $valid = $nextIterator->valid();
      }
    }
    return $valid;
  }

  protected function getCurrentIterator() {
    $iteratorIndex = $this->currentIteratorIndex;
    return isset($this->iterators[$iteratorIndex]) ? $this->iterators[$iteratorIndex] : null;
  }

  protected function getNextIterator() {
    $iteratorIndex = $this->currentIteratorIndex+1;
    return isset($this->iterators[$iteratorIndex]) ? $this->iterators[$iteratorIndex] : null;
  }

}
