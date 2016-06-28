<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Plugin\PluginSettingSubscriber;

class PluginsBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {

    $plugins = $app['config']['plugins'];
    $logger = $app['monolog'];
    $settings = $app['settings'];

    foreach($plugins as $pluginId => $pluginInfo) {

      if( !isset($pluginInfo['class']) ) {
        $logger->warn(sprintf('Cannot load plugin "%s". No class specified', $pluginId));
        continue;
      }

      // Ajout du subscriber pour la configuration du plugin
      $settings->addSubscriber(new PluginSettingSubscriber($pluginId));

      $isPluginEnabled = $settings->get('enable_plugin_'.$pluginId);
      if(!$isPluginEnabled) continue;

      $logger->debug(sprintf('Load plugin "%s" with class %s', $pluginId, $pluginInfo['class']));

      $pluginClass = $pluginInfo['class'];
      $plugin = new $pluginClass();
      $plugin->boot($app, isset($pluginInfo['options']) ? $pluginInfo['options'] : []);

    }

  }



}
