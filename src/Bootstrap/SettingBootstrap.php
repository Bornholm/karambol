<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Provider\SettingServiceProvider;
use Karambol\Setting\BaseSettingsSubscriber;

class SettingBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {
    $app->register(new SettingServiceProvider());
    $app['settings']->addSubscriber(new BaseSettingsSubscriber($app));
  }

}
