<?php

namespace Karambol\Plugin;

use Karambol\KarambolApp;

interface PluginInterface
{
  public function boot(KarambolApp $app, array $options);
}
