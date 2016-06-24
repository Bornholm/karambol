<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Provider\SettingServiceProvider;

class SettingBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {
    $app->register(new SettingServiceProvider());
  }

}
