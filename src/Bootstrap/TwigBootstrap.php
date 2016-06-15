<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Silex\Provider\TwigServiceProvider;
use League\CommonMark\CommonMarkConverter;

class TwigBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {

    // Init Twig view engine
    $app->register(new TwigServiceProvider(), [
      'twig.path' => [__DIR__.'/../Views'],
      'twig.form.templates' => ['bootstrap_3_layout.html.twig']
    ]);

    // Add default helpers
    $app['twig'] = $app->share($app->extend('twig', function($twig, $app) {

      $markdownToHTML = function($markdown) {
        $converter = new CommonMarkConverter();
        return $converter->convertToHtml($markdown);
      };

      $twig->addFunction(new \Twig_SimpleFunction('markdown', function($markdown) use ($markdownToHTML) {
        return $markdownToHTML($markdown);
      }, ['is_safe' => ['html']]));


      $twig->addFunction(new \Twig_SimpleFunction('include_markdown_file', function($markdownFile) use ($markdownToHTML) {
        $markdown = file_get_contents(realpath(__DIR__.'/../../'.$markdownFile));
        return $markdownToHTML($markdown);
      }, ['is_safe' => ['html']]));

      $twig->addFunction(new \Twig_SimpleFunction('file_exists', function($filePath) {
        return file_exists(realpath(__DIR__.'/../../'.$filePath));
      }));

      return $twig;

    }));

  }

}
