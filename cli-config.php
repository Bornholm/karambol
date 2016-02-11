<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Karambol\KarambolApp;

$app = new KarambolApp();

return ConsoleRunner::createHelperSet($app['orm']);
