<?php

namespace Karambol\RuleEngine\Action;

interface ActionInterface {
  public function exec($subject);
}