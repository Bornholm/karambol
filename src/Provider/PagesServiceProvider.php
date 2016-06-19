<?php

namespace Karambol\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Karambol\Page\PagesService;

class PagesServiceProvider implements ServiceProviderInterface
{

  public function register(Application $app) {
    $app['pages'] = new PagesService($app);
  }

  public function boot(Application $app) {}

}
