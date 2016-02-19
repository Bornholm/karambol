<?php

namespace Karambol\RuleEngine\Rule;

use Doctrine\Common\Collections\ArrayCollection;

class AlwaysFalseRule implements RuleInterface {

  public function test($subject) {
    return false;
  }

  public function setOptions(array $options) {}

}