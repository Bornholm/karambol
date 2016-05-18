<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Provider\PageServiceProvider;
use Karambol\Page;

class PageBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {

    $app->register(new PageServiceProvider());

    // Init default menu listeners
    $pageListener = new Page\DefaultPageListener($app);

    $app['page']->addListener(
      Page\PageEvent::PAGES_LIST,
      [$pageListener, 'onPageList']
    );

  }

}
