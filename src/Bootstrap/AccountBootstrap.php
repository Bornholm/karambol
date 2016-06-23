<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Provider\UserEntityProvider;
use Karambol\Provider\AccountServiceProvider;

class AccountBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {
    $app->register(new UserEntityProvider());
    $app->register(new AccountServiceProvider());
  }

}
