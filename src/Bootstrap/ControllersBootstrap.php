<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Controller;
use Karambol\RuleEngine\RuleEngine;

class ControllersBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {

    $controllers = [
      'Karambol\Controller\HomeController',
      'Karambol\Controller\Admin\AdminController',
      'Karambol\Controller\Admin\PagesController',
      'Karambol\Controller\Admin\UsersController',
      'Karambol\Controller\Admin\SettingsController',
      'Karambol\Controller\AuthenticationController',
      'Karambol\Controller\RegistrationController',
      'Karambol\Controller\PasswordController',
      'Karambol\Controller\ProfileController',
      'Karambol\Controller\DocumentationController',
    ];

    foreach($controllers as $controllerClass) {
      $ctrl = new $controllerClass();
      $ctrl->bindTo($app);
    }

    $customRulesCtrl = new Controller\Admin\RulesController(RuleEngine::CUSTOMIZATION);
    $customRulesCtrl->bindTo($app);

    $accesControlCtrl = new Controller\Admin\RulesController(RuleEngine::ACCESS_CONTROL);
    $accesControlCtrl->bindTo($app);

  }

}
