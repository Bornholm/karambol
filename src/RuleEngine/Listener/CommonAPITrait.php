<?php

namespace Karambol\RuleEngine\Listener;

use Karambol\RuleEngine\ExpressionLanguage\SimpleExpressionFunctionProvider;

Trait CommonAPITrait {

  use \Karambol\Util\AppAwareTrait;
  use \Karambol\RuleEngine\Context\VariableUnwrapperTrait;

  protected function registerCommonAPI(SimpleExpressionFunctionProvider $provider) {
    $provider->registerFunction('isConnected', [$this, 'isConnected']);
    $provider->registerFunction('log', [$this, 'log']);
  }

  public function isConnected($vars) {
    return $this->app['user'] !== null;
  }

  public function log($vars, $message) {
    $logger = $this->app['monolog'];
    $logger->info($message);
  }

}
