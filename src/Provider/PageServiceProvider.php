<?php

namespace Karambol\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Karambol\Page\PageService;

class PageServiceProvider implements ServiceProviderInterface
{

    public function register(Application $app) {
      $app['page'] = new PageService($app);
    }

    public function boot(Application $app) {}

}
