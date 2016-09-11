<?php

namespace Karambol\Plugin;

use Karambol\KarambolApp;
use Karambol\Plugin\PluginInterface;
use Karambol\Page\SystemPageSubscriber;
use Cocur\Slugify\Slugify;

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
   * @return Karambol\Plugin\Plugin
   */
  protected function registerEntities($entitiesDirectory) {
    $annotationDriver = $this->app['orm']->getConfiguration()->getMetadataDriverImpl();
    $annotationDriver->addPaths([$entitiesDirectory]);
    return $this;
  }

  /**
   * Register the plugin's views
   *
   * @param string $viewsDirectory The absolute path to the views base directory
   * @return Karambol\Plugin\Plugin
   */
  public function registerViews($viewsDirectory)
  {
    $twigPaths = $this->app['twig.path'];
    array_unshift($twigPaths, $viewsDirectory);
    $this->app['twig.path'] = $twigPaths;
    return $this;
  }

  /**
   * Register the plugin's controllers
   *
   * @param array $controllers An array of the controller's classes to mount
   * @return Karambol\Plugin\Plugin
   */
  protected function registerControllers(array $controllers) {
    foreach($controllers as $controllerClass) {
      $ctrl = new $controllerClass();
      $ctrl->bindTo($this->app);
    }
    return $this;
  }

  /**
   * Register a YAML translation file
   *
   * @param string $language The language of the translations
   * @param string $translationFile The absolute path to the YAML translation file
   * @return Karambol\Plugin\Plugin
   */
  protected function registerTranslation($language, $translationFile) {
    $this->app['translator'] = $this->app->share($this->app->extend('translator', function($translator) use ($language, $translationFile) {
      $translator->addResource('yaml', $translationFile, $language);
      return $translator;
    }));
    return $this;
  }

  /**
   * Register a new page
   *
   * @param string $language The language of the translations
   * @param string $translationFile The absolute path to the YAML translation file
   * @return Karambol\Plugin\Plugin
   */
  protected function registerSystemPage($pageLabel, $pageUrl, $pageSlug = null) {
    if($pageSlug === null) {
      $slugify = new Slugify();
      $pageSlug = $slugify->slugify($pageLabel, '-');
    }
    $this->app['pages']->addSubscriber(new SystemPageSubscriber($pageLabel, $pageUrl, $pageSlug));
    return $this;
  }

}
