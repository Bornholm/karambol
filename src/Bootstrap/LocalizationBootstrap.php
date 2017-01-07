<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Silex\Provider\TranslationServiceProvider;
use Symfony\Component\Translation\Loader\YamlFileLoader;

/**
 * Initialisation locale
 * @package Karambol
 * @license AGPLv3
 * @author William Petit
 */
class LocalizationBootstrap implements BootstrapInterface {
  
  /**
   * Initialisation
   * @param KarambolApp $app
   * @author William Petit
   */
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
