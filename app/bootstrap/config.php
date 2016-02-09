<?php

  $configDir = __DIR__.'/../../config';

  $defaultConfig = $configDir.'/default.yml';
  $app->register(new Karambol\Providers\YamlConfigServiceProvider($defaultConfig));

  $hostConfig = $configDir.'/'.gethostname().'.yml';
  if(file_exists($hostConfig)) {
    $app->register(new Karambol\Providers\YamlConfigServiceProvider($hostConfig));
  }

  // Activate debug
  $app['debug'] = $app['config']['debug'];
