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
use Karambol\Plugin\PluginSettingSubscriber;

/**
 * Initialisation plugin
 * @package Karambol
 * @license AGPLv3
 * @author William Petit
 */
class PluginsBootstrap implements BootstrapInterface {
  
  /**
   * Initialisation
   * @param KarambolApp $app
   * @author William Petit
   */
  public function bootstrap(KarambolApp $app) {

    $config = $app['config'];
    $plugins = isset($config['plugins']) ? $app['config']['plugins'] : [];
    $logger = $app['monolog'];
    $settings = $app['settings'];

    foreach($plugins as $pluginName => $pluginInfo) {

      if( !isset($pluginInfo['class']) ) {
        $logger->warn(sprintf('Cannot load plugin "%s". No class specified', $pluginName));
        continue;
      }

      // Ajout du subscriber pour la configuration du plugin
      $settings->addSubscriber(new PluginSettingSubscriber($app, $pluginName));

      $isPluginEnabled = $settings->get('enable_plugin_'.$pluginName);
      if(!$isPluginEnabled) continue;

      $logger->debug(sprintf('Load plugin "%s" with class %s', $pluginName, $pluginInfo['class']));

      $pluginClass = $pluginInfo['class'];

      if(!class_exists($pluginClass)) {
        $logger->error(sprintf('The "%s" plugin\'s class "%s" is not defined. Check your composer dependencies.', $pluginName, $pluginClass));
        continue;
      }

      try {
        $plugin = new $pluginClass();
        $plugin->boot($app, isset($pluginInfo['options']) ? $pluginInfo['options'] : []);
      } catch(\Exception $ex) {
        $logger->error(sprintf('Error while loading plugin "%s" with class "%s" !', $pluginName, $pluginClass));
        $logger->error($ex);
      }

    }

  }



}
