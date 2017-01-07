<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Controller;
use Karambol\RuleEngine\RuleEngine;

/**
 * Initialisation controller
 * @package Karambol
 * @since 1.0.0
 * @license AGPLv3
 * @author William Petit
 */
class ControllersBootstrap implements BootstrapInterface {
  
  /**
   * Initialisation
   * @param KarambolApp $app
   * @author William Petit
   */
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
