<?php

namespace Karambol\RuleEngine;

use Karambol\RuleEngine\RuleEngineEvent;
use Karambol\RuleEngine\RuleEngineService;
use Karambol\KarambolApp;
use Karambol\Menu\MenuItem;
use Karambol\Page\PageInterface;
use Karambol\Page\Page;
use Karambol\Entity\User;

class CommonAPIConfigurator {

  protected $app;

  public function __construct(KarambolApp $app) {
    $this->app = $app;
  }

  protected function registerCommonAPI($provider) {

    $app = $this->app;

    $provider->registerFunction(
      'isConnected',
      function($vars) use ($app) {
        return $app['user'] !== null;
      }
    );

    $provider->registerFunction(
      'isGranted',
      function($vars, $authorization, $authorizationAttrs = []) use ($app) {
        return $app['security.authorization_checker']->isGranted($authorization, $authorizationAttrs);
      }
    );

    $provider->registerFunction(
      'log',
      function($vars, $message) use ($app) {
        $logger = $app['monolog'];
        $logger->info($message);
      }
    );

  }

}
