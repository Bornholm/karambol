<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;

class PluginsBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {

    $plugins = $app['config']['plugins'];
    $logger = $app['monolog'];

    foreach($plugins as $pluginId => $pluginInfo) {

      if( !isset($pluginInfo['class']) ) {
        $logger->warn(sprintf('Cannot load plugin "%s". No class specified', $pluginId));
        continue;
      }

      $logger->debug(sprintf('Load plugin "%s" with class %s', $pluginId, $pluginInfo['class']));

      $pluginClass = $pluginInfo['class'];
      $plugin = new $pluginClass();
      $plugin->boot($app, isset($pluginInfo['options']) ? $pluginInfo['options'] : []);

    }

  }

}
