<?php

namespace Karambol\RuleEngine\Rule;

class AlwaysTrueRule implements RuleInterface {

  public function test($subject) {
    return true;
  }

  public function setOptions(array $options) {}
  public function getOptions() {}

}
