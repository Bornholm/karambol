<?php

namespace Karambol\RuleEngine\Rule;

class AlwaysFalseRule implements RuleInterface {

  public function test($subject) {
    return false;
  }

  public function setOptions(array $options) {}
  public function getOptions() {}

}
