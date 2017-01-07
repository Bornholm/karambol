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
use Karambol\AccessControl\Parser\ResourceSelectorParser;
use Karambol\AccessControl\Resource;
use Karambol\Twig\CommonMarkExtension;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use League\CommonMark\CommonMarkConverter;
use Colors\RandomColor;

/**
 * Initialisation twig
 * @package Karambol
 * @license AGPLv3
 * @author William Petit
 */
class TwigBootstrap implements BootstrapInterface {
  
  /**
   * Initialisation
   * @param KarambolApp $app
   * @author William Petit
   */
  public function bootstrap(KarambolApp $app) {

    $app->register(new HttpFragmentServiceProvider());

    // Init Twig view engine
    $app->register(new TwigServiceProvider(), [
      'twig.path' => [__DIR__.'/../Views'],
      'twig.form.templates' => ['bootstrap_3_layout.html.twig']
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
