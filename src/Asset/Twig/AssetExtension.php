<?php

namespace Karambol\Asset\Twig;

use \Twig_Extension;
use \Twig_SimpleFunction;
use \Twig_Environment;

/*

$toPublicUrl = function($assetPublicPath) use ($app) {
  $req = $app['request'];
  $baseUrl = $req->getBasePath();
  return sprintf($baseUrl.'/%s', ltrim($assetPublicPath, '/'));
};

$twig->addFunction(new \Twig_SimpleFunction('asset', function($assetPublicPath) use ($toPublicUrl) {
  return $toPublicUrl($assetPublicPath);
}));

$twig->addFunction(new \Twig_SimpleFunction('appendScript', function($scriptPaths) use ($app) {
  $scriptPaths = !is_array($scriptPaths) ? [$scriptPaths] : $scriptPaths;
  $app['assets']->appendScripts($scriptPaths);
}));

$twig->addFunction(new \Twig_SimpleFunction('renderScripts', function() use ($app, $toPublicUrl) {

  $debug = $app['debug'];
  $assetsSvc = $app['assets'];

  $scriptTag = '<script src="%s"></script>';
  $tags = '';

  if($debug) {
    foreach($assetsSvc->getScripts() as $script) {
      $tags .= sprintf($scriptTag, $toPublicUrl($script));
    }
  } else {
    $cachedScript = $assetsSvc->packScripts();
    $tags = sprintf($scriptTag, $toPublicUrl($cachedScript));
  }

  return $tags;

}, ['is_safe' => ['html', 'js']]));

*/

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

  public function appendScript($scriptPublicPath) {
    $scriptPublicPath = !is_array($scriptPublicPath) ? [$scriptPublicPath] : $scriptPublicPath;
    $this->app['assets']->appendScripts($scriptPublicPath);
  }

  public function renderScripts() {

    $debug = $this->app['debug'];
    $assetsSvc = $this->app['assets'];

    $scriptTag = '<script src="%s"></script>';
    $tags = '';

    if($debug) {
      foreach($assetsSvc->getScripts() as $script) {
        $tags .= sprintf($scriptTag, $this->pathToUrl($script));
      }
    } else {
      $cachedScript = $assetsSvc->packScripts();
      $tags = sprintf($scriptTag, $this->pathToUrl($cachedScript));
    }

    return $tags;

  }

  protected function pathToUrl($assetPublicPath) {
    $req = $this->app['request'];
    $baseUrl = $req->getBasePath();
    return sprintf($baseUrl.'/%s', ltrim($assetPublicPath, '/'));
  }

}
