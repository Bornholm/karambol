<?php

namespace Karambol\RuleEngine;

interface RuleInterface
{
  public function getCondition();
  public function getAction();
}
