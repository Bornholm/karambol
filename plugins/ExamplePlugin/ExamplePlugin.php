<?php

namespace ExamplePlugin;

use Karambol\Plugin\PluginInterface;
use Karambol\KarambolApp;
use ExamplePlugin\Controller\ExampleController;

class ExamplePlugin implements PluginInterface
{

  public function boot(KarambolApp $app) {

    $app['twig.path'] = array_merge($app['twig.path'], array(__DIR__.'/views'));

    $exampleCtrl = new ExampleController();
    $exampleCtrl->mount($app);

  }

}
