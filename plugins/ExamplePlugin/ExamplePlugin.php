<?php

namespace ExamplePlugin;

use Karambol\Plugin\PluginInterface;
use Karambol\KarambolApp;
use ExamplePlugin\Controller\ExampleController;

class ExamplePlugin implements PluginInterface
{

  public function boot(KarambolApp $app, array $options) {

    $app['twig.path'] = array_merge($app['twig.path'], array(__DIR__.'/Views'));

    $exampleCtrl = new ExampleController();
    $exampleCtrl->bindTo($app);

  }

}
