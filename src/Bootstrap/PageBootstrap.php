<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Provider\PageServiceProvider;
use Karambol\Page\BasePagesSubscriber;

class PageBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {
    $app->register(new PageServiceProvider());
    $app['pages']->addSubscriber(new BasePagesSubscriber($app));
  }

}
