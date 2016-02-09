<?php

namespace Karambol\Interfaces;

use Silex\Application;

interface PluginInterface
{
  public function boot(Application $app);
}
