<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Plugin\PluginSettingSubscriber;

class PluginsBootstrap implements BootstrapInterface {

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
      $plugin = new $pluginClass();
      $plugin->boot($app, isset($pluginInfo['options']) ? $pluginInfo['options'] : []);

    }

  }



}
