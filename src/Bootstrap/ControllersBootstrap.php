<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Controller;

class ControllersBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {

    // Homepage controllers
    $homeCtrl = new Controller\HomeController();
    $homeCtrl->bindTo($app);

    // Admin controllers
    $adminCtrl = new Controller\Admin\AdminController();
    $adminCtrl->bindTo($app);

    $adminUsersCtrl = new Controller\Admin\UsersController();
    $adminUsersCtrl->bindTo($app);

  }

}
