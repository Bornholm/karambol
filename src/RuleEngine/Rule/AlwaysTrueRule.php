<?php

namespace Karambol\RuleEngine\Rule;

use Doctrine\Common\Collections\ArrayCollection;

class AlwaysTrueRule implements RuleInterface {

  public function test($subject) {
    return true;
  }

  public function setOptions(array $options) {}

}