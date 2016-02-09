<?php

use Silex\Application;

interface Plugin
{
  public function boot(Application $app);
}
