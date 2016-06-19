<?php

namespace Karambol\VirtualSet;

use Symfony\Component\EventDispatcher\Event;

class ItemIterateEvent extends Event {

  const NAME = 'virtualset.iterate';

  protected $iterator;

  public function __construct() {
    $this->iterator = new QueuedIterator();
  }

  public function addIterator(\Iterator $iterator) {
    $this->iterator->append($iterator);
    return $this;
  }

  public function getIteratorAggregate() {
    return $this->iterator;
  }

}
