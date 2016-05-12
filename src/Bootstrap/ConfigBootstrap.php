<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Provider;

class ConfigBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {

    $configDir = __DIR__.'/../../config';

    $defaultConfig = $configDir.'/default.yml';
    $app->register(new Provider\YamlConfigServiceProvider($defaultConfig));

    $locals = glob($configDir.'/local.d/*.yml');
    foreach($locals as $localConfig) {
      $app->register(new Provider\YamlConfigServiceProvider($localConfig));
    }

    // Activate debug
    $app['debug'] = $app['config']['debug'];

  }

}
