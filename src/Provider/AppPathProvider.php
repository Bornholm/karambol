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
    return realpath(join(DIRECTORY_SEPARATOR, [__DIR__, '..', '..']));
  }

  public function getPath($appRelativePath) {
    return join(DIRECTORY_SEPARATOR, [$this->getAppDirectory(), $appRelativePath]);
  }

  /**
   * Check if the path exists in the app directory tree
   *
   * @param string $relativePath The relative path from the app root directory
   * @return boolean
   */
  public function existsInApp($relativePath) {
    $filePath = realpath($this->getPath($relativePath));
    return strpos($filePath, $this->getAppDirectory()) === 0;
  }

  /**
   * Return the absolute path to the public directory
   *
   * @param string $subpath A relative path of a desired file from the public directory
   * @return string The path
   */
  public function getPublicDir($subPath = '') {
    return $this->getPath(join(DIRECTORY_SEPARATOR, ['public', $subPath]));
  }

  /**
   * Return the absolute path to the public cache directory
   *
   * @param string $subpath A relative path of a desired file from the public cache directory
   * @return string The path
   */
  public function getPublicCacheDir($subPath = '') {
    return $this->getPublicDir(join(DIRECTORY_SEPARATOR, ['cache', $subPath]));
  }

  /**
   * Return the absolute path to the data directory
   *
   * @param string $subpath A relative path of a desired file from the data directory
   * @return string The path
   */
  public function getDataDir($subPath = '') {
    return $this->getDataDir(join(DIRECTORY_SEPARATOR, ['data', $subPath]));
  }

  /**
   * Return the absolute path to the cache directory
   *
   * @param string $subpath A relative path of a desired file from the cache directory
   * @return string The path
   */
  public function getCacheDir($subPath = '') {
    return $this->getDataDir(join(DIRECTORY_SEPARATOR, ['cache', $subPath]));
  }

}
