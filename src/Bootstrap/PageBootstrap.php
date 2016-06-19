<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Provider\PagesServiceProvider;
use Karambol\Page\BasePagesSubscriber;

class PageBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {
    $app->register(new PagesServiceProvider());
    $app['pages']->addSubscriber(new BasePagesSubscriber($app));
  }

}
