<?php

namespace DashboardPlugin;

use Karambol\Plugin\PluginInterface;
use Karambol\KarambolApp;
use DashboardPlugin\Controller\DashboardController;

class DashboardPlugin implements PluginInterface
{

  public function boot(KarambolApp $app, array $options) {
    $this->addPluginPages($app);
    $this->addPluginTranslation($app);
    $this->addControllers($app);
  }

  public function addPluginTranslation(KarambolApp $app) {
    $app['translator'] = $app->share($app->extend('translator', function($translator) {
      $translator->addResource('yaml', __DIR__.'/locales/fr.yml', 'fr');
      return $translator;
    }));
  }

  public function addPluginPages(KarambolApp $app) {
    $app['pages']->addSubscriber(new DashboardPagesSubscriber());
  }

  public function addControllers(KarambolApp $app) {
    $dashboardCtrl = new DashboardController();
    $dashboardCtrl->bindTo($app);
  }

}
