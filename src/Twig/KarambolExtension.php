<?php

namespace Karambol\Twig;

use \Twig_Extension;
use \Twig_SimpleFunction;
use \Twig_Environment;

class KarambolExtension extends Twig_Extension {

  use \Karambol\Util\AppAwareTrait;

  public function getFunctions() {
    return [];
  }

  public function getName() {
    return 'karambol_karambol_extension';
  }

}
