<?php

namespace Karambol\RuleEngine;

use Karambol\RuleEngine\RuleEngineEvent;
use Karambol\RuleEngine\RuleEngineService;
use Karambol\KarambolApp;
use Karambol\Menu\MenuItem;
use Karambol\Page\PageInterface;
use Karambol\Page\Page;
use Karambol\Entity\User;
use Karambol\AccessControl\Resource;
use Karambol\AccessControl\BaseActions;
use Karambol\AccessControl\Parser\ResourceSelectorParser;

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
      function($vars, $actionOrRole, $resourceSelector = null) use ($app) {

        $authChecker = $app['security.authorization_checker'];

        if($resourceSelector === null) {
          $action = BaseActions::ROLE;
          $resource = new Resource('role', $actionOrRole);
          return $authChecker->isGranted($action, $resource);
        }

        $action = $actionOrRole;

        $parser = new ResourceSelectorParser();
        $selector = $parser->parse($resourceSelector);

        $resources = $selector->getAssociatedResources();

        foreach($resources as $resource) {
          if($authChecker->isGranted($action, $resource)) return true;
        }

        return false;

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
