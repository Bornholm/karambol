<?php

namespace Karambol\Twig;

use \Twig_Extension;
use \Twig_SimpleFunction;
use \Twig_Environment;

class DebugBarExtension extends Twig_Extension {

  use \Karambol\Util\AppAwareTrait;

  public function getFunctions() {
    return [
      new Twig_SimpleFunction('debugbarHead', [$this, 'handleDebugBarHead'], ['is_safe' => ['html', 'js']]),
      new Twig_SimpleFunction('debugbar', [$this, 'handleDebugBar'], ['is_safe' => ['html', 'js']])
    ];
  }

  public function getName() {
    return 'karambol_debugbar_extension';
  }

  public function handleDebugBarHead() {
    if(!$this->isDebugMode()) return;
    $renderer =$this->getDebugBarRenderer();
    return $renderer->renderHead();
  }

  public function handleDebugBar() {
    if(!$this->isDebugMode()) return;
    $renderer = $this->getDebugBarRenderer();
    return $renderer->render();
  }

  protected function getDebugBarRenderer() {
    $debugBar = $this->app['debug_bar'];
    $req = $this->app['request'];
    $baseUrl = $req->getBasePath();
    return $debugBar->getJavascriptRenderer($baseUrl.'/vendor/debugbar');
  }

  protected function isDebugMode() {
    return $this->app['debug'] === true;
  }

}
