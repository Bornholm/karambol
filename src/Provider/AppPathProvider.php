<?php

namespace Karambol\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\Filesystem\Filesystem;

class AppPathProvider implements ServiceProviderInterface
{

  public function register(Application $app) {
    $app['app_path'] = new AppPathService();
  }

  public function boot(Application $app) {}

}

class AppPathService {

  protected $fs;

  public function __construct() {
    $this->fs = new Filesystem();
  }

  public function getAppDirectory() {
    return join(DIRECTORY_SEPARATOR, [__DIR__, '..', '..']);
  }

  public function getPath($appRelativePath) {
    return join(DIRECTORY_SEPARATOR, [$this->getAppDirectory(), $appRelativePath]);
  }

}
