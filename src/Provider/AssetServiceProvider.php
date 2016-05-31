<?php

namespace Karambol\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Karambol\Asset\AssetService;

class AssetServiceProvider implements ServiceProviderInterface
{

    public function register(Application $app) {

      $app['asset'] = new AssetService(__DIR__.'/../../public');

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
          $app['asset']->appendScripts($scriptPaths);
        }));

        $twig->addFunction(new \Twig_SimpleFunction('renderScripts', function() use ($app, $toPublicUrl) {

          $debug = $app['debug'];
          $assetService = $app['asset'];

          $scriptTag = '<script src="%s"></script>';
          $tags = '';

          if($debug) {
            foreach($assetService->getScripts() as $script) {
              $tags .= sprintf($scriptTag, $toPublicUrl($script));
            }
          } else {
            $cachedScript = $assetService->packScripts();
            $tags = sprintf($scriptTag, $toPublicUrl($cachedScript));
          }

          return $tags;

        }, ['is_safe' => ['html', 'js']]));

        return $twig;

      }));

    }

    public function boot(Application $app) {}

}
