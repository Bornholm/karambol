<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Provider;

class AssetBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {

    // Register asset service
    $app->register(new Provider\AssetServiceProvider());

    $app['twig'] = $app->share($app->extend('twig', function($twig, $app) {

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

      return $twig;

    }));

  }

}
