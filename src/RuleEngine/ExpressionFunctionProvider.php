<?php

namespace Karambol\RuleEngine;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

class ExpressionFunctionProvider implements ExpressionFunctionProviderInterface
{

  protected $functions = [];

  public function registerFunction($functionName, $compilerCallback, $evaluateCallback) {
    $this->functions[] = new ExpressionFunction($functionName, $compilerCallback, $evaluateCallback);
    return $this;
  }

  public function getFunctions()
  {
    return $this->functions;
  }

}
