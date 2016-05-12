<?php

namespace Karambol\Theme;

use Karambol\KarambolApp;
use Karambol\Theme\ThemeChangeEvent;

class ThemeListener {

  protected $previousThemePath;
  protected $app;

  public function __construct(KarambolApp $app) {
    $this->app = $app;
    $this->previousThemePath = null;
  }

  public function onThemeChange(ThemeChangeEvent $event) {

    $app = $this->app;
    $theme = $app['theme'];
    $previousThemePath = $this->previousThemePath;
    $twigPaths = $app['twig.path'];

    // Remove previous theme path if any
    if(!empty($previousThemePath) && ($key = array_search($previousThemePath, $twigPaths)) !== false) {
      unset($twigPaths[$key]);
    }

    // Prepend new theme path to Twig loader
    $newThemePath = $theme->getSelectedThemeDir();
    array_unshift($twigPaths, $newThemePath);
    $app['twig.path'] = $twigPaths;

    // Save new theme path for later
    $this->previousThemePath = $newThemePath;

  }

}
