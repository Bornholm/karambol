<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Controller;
use Karambol\Entity\RuleSet;

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

    $customRulesCtrl = new Controller\Admin\RulesController(RuleSet::CUSTOMIZATION);
    $customRulesCtrl->bindTo($app);

  }

}
