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
