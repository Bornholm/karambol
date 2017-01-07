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
use Karambol\Provider;
use Karambol\Asset\Twig\AssetExtension;

/**
 * Initialisation asset
 * @package Karambol
 * @license AGPLv3
 * @author William Petit
 */
class AssetBootstrap implements BootstrapInterface {
  
  /**
   * Initialisation
   * @param KarambolApp $app
   * @author William Petit
   */
  public function bootstrap(KarambolApp $app) {

    // Register asset service
    $app->register(new Provider\AssetServiceProvider());

    // Add twig extension
    $app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
      $twig->addExtension(new AssetExtension($app));
      return $twig;
    }));

  }

}
