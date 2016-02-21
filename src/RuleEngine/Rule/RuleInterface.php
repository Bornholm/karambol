<?php

namespace Karambol\RuleEngine\Rule;

interface RuleInterface {
  public function test($subject);
  public function setOptions(array $options);
  public function getOptions();
}
