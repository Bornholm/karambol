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

    // Init Twig view engine
    $app->register(new TwigServiceProvider(), [
      'twig.path' => [__DIR__.'/../Views'],
      'twig.form.templates' => ['bootstrap_3_layout.html.twig']
    ]);

    // Add default helpers
    $app['twig'] = $app->share($app->extend('twig', function($twig, $app) {

      $twig->getLoader()->addLoader(new \Twig_Loader_String());

      $twig->addExtension(new CommonMarkExtension($app));

      $twig->addFunction(new \Twig_SimpleFunction('file_exists', function($filePath) {
        return file_exists(realpath(__DIR__.'/../../'.$filePath));
      }));

      $twig->addFunction(new \Twig_SimpleFunction('reset_color', function($seed = 0) {
        mt_srand($seed);
      }));

      $twig->addFunction(new \Twig_SimpleFunction('resource', function($resourceType, $resourceId) {
        return new Resource($resourceType, $resourceId);
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
