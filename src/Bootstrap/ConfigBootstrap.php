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

/**
 * Initialisation asset
 * @package Karambol
 * @license AGPLv3
 * @author William Petit
 */
class ConfigBootstrap implements BootstrapInterface {
  
  /**
   * Initialisation
   * @param KarambolApp $app
   * @author William Petit
   */
  public function bootstrap(KarambolApp $app) {

    $configDir = __DIR__.'/../../config';

    $defaultConfig = $configDir.'/default.yml';
    $app->register(new Provider\YamlConfigServiceProvider($defaultConfig));

    $locals = glob($configDir.'/local.d/*.yml');
    foreach($locals as $localConfig) {
      $app->register(new Provider\YamlConfigServiceProvider($localConfig));
    }

    // Expose debug mode
    $app['debug'] = $app['config']['debug'];

  }

}
