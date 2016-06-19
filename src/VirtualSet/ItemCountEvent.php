<?php

namespace Karambol\VirtualSet;

use Symfony\Component\EventDispatcher\Event;

class ItemCountEvent extends Event {

  const NAME = 'virtualset.count';

  protected $total = 0;

  public function add($amount) {
    $this->total += $amount;
    return $this;
  }

  public function sub($amount) {
    $this->total -= $amount;
    return $this;
  }

  public function getTotal() {
    return $this->total;
  }

}
