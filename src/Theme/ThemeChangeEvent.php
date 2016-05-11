<?php

namespace Karambol\Theme;

use Symfony\Component\EventDispatcher\Event;

class ThemeChangeEvent extends Event {

  protected $theme;
  protected $previousTheme;

  public function __construct($previousTheme, $theme) {
    $this->theme = $theme;
    $this->previousTheme = $previousTheme;
  }

  public function getTheme() {
    return $this->theme;
  }

  public function getPreviousTheme() {
    return $this->previousTheme;
  }

}
