<?php

namespace Karambol\Plugin;

use Karambol\KarambolApp;
use Karambol\Plugin\PluginInterface;

class Plugin implements PluginInterface {

  /**
   * @var Karambol\KarambolApp
   */
  protected $app;

  public function boot(KarambolApp $app, array $options) {
    $this->app = $app;
  }

  /**
   * Register the plugin's ORM entities
   *
   * @param string $entitiesDirectory The absolute path to the enities base directory
   */
  protected function registerEntities(string $entitiesDirectory) {
    $annotationDriver = $this->app['orm']->getConfiguration()->getMetadataDriverImpl();
    $annotationDriver->addPaths([$entitiesDirectory]);
  }

  /**
   * Register the plugin's views
   *
   * @param string $viewsDirectory The absolute path to the views base directory
   */
  public function registerViews($viewsDirectory)
  {
    $twigPaths = $this->app['twig.path'];
    array_unshift($twigPaths, $viewsDirectory);
    $this->app['twig.path'] = $twigPaths;
  }

  /**
   * Register the plugin's controllers
   *
   * @param array $controllers An array of the controller's classes to mount
   */
  protected function registerControllers(array $controllers) {
    foreach($controllers as $controllerClass) {
      $ctrl = new $controllerClass();
      $ctrl->bindTo($this->app);
    }
  }

  /**
   * Register a YAML translation file
   *
   * @param string $language The language of the translations
   * @param string $translationFile The absolute path to the YAML translation file
   */
  protected function registerTranslation($language, $translationFile) {
    $app['translator'] = $app->share($app->extend('translator', function($translator) {
      $translator->addResource('yaml', $translationFile, $language);
      return $translator;
    }));
  }


}
