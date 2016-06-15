<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Controller;
use Karambol\RuleEngine\RuleEngineService;

class ControllersBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {

    $controllers = [
      'Karambol\Controller\HomeController',
      'Karambol\Controller\Admin\AdminController',
      'Karambol\Controller\Admin\PagesController',
      'Karambol\Controller\Admin\UsersController',
      'Karambol\Controller\AuthenticationController'
    ];

    foreach($controllers as $controllerClass) {
      $ctrl = new $controllerClass();
      $ctrl->bindTo($app);
    }

    $customRulesCtrl = new Controller\Admin\RulesController(RuleEngineService::CUSTOMIZATION);
    $customRulesCtrl->bindTo($app);

    $accesControlCtrl = new Controller\Admin\RulesController(RuleEngineService::ACCESS_CONTROL);
    $accesControlCtrl->bindTo($app);

  }

}
