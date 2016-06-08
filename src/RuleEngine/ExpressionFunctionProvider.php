<?php

namespace Karambol\RuleEngine;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

class ExpressionFunctionProvider implements ExpressionFunctionProviderInterface
{

  protected $functions = [];

  public function registerFunction($functionName, $evaluateCallback) {
    $this->functions[] = new ExpressionFunction(
      $functionName,
      function() { return 'throw new \Exception(\'This expression is not meant to be compiled !\')'; },
      $evaluateCallback
    );
    return $this;
  }

  public function getFunctions()
  {
    return $this->functions;
  }

}
