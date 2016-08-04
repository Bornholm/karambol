<?php

namespace Karambol\Plugin;

use Karambol\KarambolApp;

interface PluginInterface
{
  /**
   * Invoked by Karambol when the plugin is loaded
   *
   * @param Karambol\KarambolApp $app     The parent Karambol application
   * @param array $options The array representation of the plugin configuration
   */
  public function boot(KarambolApp $app, array $options);
}
