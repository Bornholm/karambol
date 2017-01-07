<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\AccessControl\Parser\ResourceSelectorParser;
use Karambol\AccessControl\Resource;
use Karambol\Twig\CommonMarkExtension;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use League\CommonMark\CommonMarkConverter;
use Colors\RandomColor;

class TwigBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {

    $app->register(new HttpFragmentServiceProvider());

    /* @var Karambol/Provider/AppPathprovider/AppPathService */
    $appPath = $app['app_path'];

    // Initialize Twig options
    $twigOptions = [];

    // If not in debug mode, activate twig cache
    if(!$app['debug']) {
      $twigCacheDir = $appPath->getCacheDir('twig');
      if(!is_dir($twigCacheDir)) mkdir($twigCacheDir, 0755, true);
      $twigOptions['cache'] = $twigCacheDir;
    }


    // Init Twig view engine
    $app->register(new TwigServiceProvider(), [
      'twig.path' => [__DIR__.'/../Views'],
      'twig.form.templates' => ['bootstrap_3_layout.html.twig'],
      'twig.options' => $twigOptions
    ]);

    // Add default helpers
    $app['twig'] = $app->share($app->extend('twig', function($twig, $app) {

      $twig->addExtension(new CommonMarkExtension($app));

      $twig->addFunction(new \Twig_SimpleFunction('file_exists', function($filePath) {
        return file_exists(realpath(__DIR__.'/../../'.$filePath));
      }));

      $twig->addFunction(new \Twig_SimpleFunction('reset_color', function($seed = 0) {
        mt_srand($seed);
      }));

      $twig->addFunction(new \Twig_SimpleFunction('resource', function($resourceType, $resourceId, $resourcePropertyName = null) {
        return new Resource($resourceType, $resourceId, $resourcePropertyName);
      }));

      $twig->addFunction(new \Twig_SimpleFunction('random_color', function($luminosity = 'light', $hue = null, $format = 'rgbCss') {
        return RandomColor::one(array(
          'luminosity' => $luminosity,
          'hue' => $hue,
          'format' => $format
        ));
      }));

      return $twig;

    }));

  }

}
