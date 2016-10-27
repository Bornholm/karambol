<?php

namespace Karambol\RuleEngine\Context;

use Karambol\RuleEngine\Context\Variable;

trait VariableUnwrapperTrait {

  protected function unwrap($variable) {
    return $variable instanceof Variable ? $variable->getSource() : $variable;
  }

}
