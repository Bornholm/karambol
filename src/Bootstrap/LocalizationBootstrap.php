<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Silex\Provider\TranslationServiceProvider;
use Symfony\Component\Translation\Loader\YamlFileLoader;

class LocalizationBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {

    $app->register(new TranslationServiceProvider(), [
      'locale' => 'fr',
      'locale_fallbacks' => array('en')
    ]);

    $app['translator'] = $app->share($app->extend('translator', function($translator) {

      $translator->addLoader('yaml', new YamlFileLoader());

      $baseDir = __DIR__.'/../../locales';
      $transFiles = glob($baseDir.'/*.yml');

      foreach($transFiles as $file) {
        $lang = basename($file, '.yml');
        $translator->addResource('yaml', $file, $lang);
      }

      return $translator;

    }));
  }

}
