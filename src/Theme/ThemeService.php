<?php

namespace Karambol\Theme;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Karambol\Theme\ThemeEvent;
use Karambol\Theme\ThemeChangeEvent;

class ThemeService extends EventDispatcher {

  const THEME_CHANGE = 'theme.event.theme_change';

  protected $themesBaseDir = '';
  protected $availableThemes = [];
  protected $defaultTheme = '';
  protected $theme = null;

  public function __construct($themesBaseDir, array $availableThemes) {
    $this->themesBaseDir = $themesBaseDir;
    $this->availableThemes = $availableThemes;
  }

  public function getDefaultTheme() {
    return $this->defaultTheme;
  }

  public function setDefaultTheme($defaultTheme = null) {
    if( $defaultTheme !== null && !$this->isThemeAvailable($defaultTheme) ) {
      throw new \Exception(sprintf('The theme "%s" is not available !', $defaultTheme));
    }
    $this->defaultTheme = $defaultTheme;
    return $this;
  }

  public function setSelectedTheme($newTheme = null) {
    if( $newTheme !== null && !$this->isThemeAvailable($newTheme) ) {
      throw new \Exception(sprintf('The theme "%s" is not available !', $newTheme));
    }
    $this->theme = $newTheme;
    $event = new ThemeChangeEvent($this->theme, $this->getSelectedTheme());
    $this->dispatch(self::THEME_CHANGE, $event);
    return $this;
  }

  public function getSelectedTheme() {
    return empty($this->theme) ? $this->getDefaultTheme() : $this->theme;
  }

  public function getSelectedThemeDir() {
    return $this->themesBaseDir.'/'.$this->getSelectedTheme();
  }

  public function isThemeAvailable($theme) {
    return in_array($theme, $this->getAvailableThemes());
  }

  public function getAvailableThemes() {
    return $this->availableThemes;
  }

}
