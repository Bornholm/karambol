<?php

namespace Karambol\Asset\Twig;

use \Twig_Extension;
use \Twig_SimpleFunction;
use \Twig_Environment;

class AssetExtension extends Twig_Extension {

  use \Karambol\Util\AppAwareTrait;

  public function getFunctions() {
    return [
      new Twig_SimpleFunction('asset', [$this, 'getAssetUrl'], ['is_safe' => ['html']]),
      new Twig_SimpleFunction('appendScript', [$this, 'appendScript'], ['is_safe' => ['html', 'js']]),
      new Twig_SimpleFunction('renderScripts', [$this, 'renderScripts'], ['is_safe' => ['html', 'js']])
    ];
  }

  public function getName() {
    return 'karambol_asset_extension';
  }

  public function getAssetUrl($assetPublicPath) {
    return $this->pathToUrl($assetPublicPath);
  }

  public function appendScript($scriptPublicPath, $data = []) {
    $scriptPublicPath = !is_array($scriptPublicPath) ? [$scriptPublicPath] : $scriptPublicPath;
    $this->app['assets']->appendScripts($scriptPublicPath, $data);
  }

  public function renderScripts() {

    $debug = $this->app['debug'];
    $assetsSvc = $this->app['assets'];

    $scriptTag = '<script src="%s" %s></script>';
    $tags = '';

    $dataAttributes = $this->dataToAttributes($assetsSvc->getData());

    if($debug) {
      foreach($assetsSvc->getScripts() as $script) {
        $tags .= sprintf($scriptTag, $this->pathToUrl($script), $dataAttributes);
      }
    } else {
      $cachedScript = $assetsSvc->packScripts();
      $tags = sprintf($scriptTag, $this->pathToUrl($cachedScript), $dataAttributes);
    }

    return $tags;

  }

  protected function pathToUrl($assetPublicPath) {
    $req = $this->app['request'];
    $baseUrl = $req->getBasePath();
    return sprintf($baseUrl.'/%s', ltrim($assetPublicPath, '/'));
  }

  protected function dataToAttributes(array $data) {
    $attrs = '';
    foreach($data as $key => $value) {
      $serializedValue = is_array($value) ? json_encode($value) : $value;
      $attrs .= sprintf('data-%s="%s"', $key, htmlspecialchars($serializedValue, ENT_QUOTES, 'UTF-8'));
    }
    return $attrs;
  }

}
