<?php

namespace Karambol\Asset;
use Karambol\Provider\AppPathService;

class AssetService {

  protected $scripts = [];
  protected $stylesheets = [];
  protected $publicDir;

  public function __construct(AppPathService $appPath) {
    $this->appPath = $appPath;
  }

  public function appendScripts(array $scripts) {
    foreach($scripts as $sc) {
      if(!in_array($sc, $this->scripts)) $this->scripts[] = $sc;
    }
  }

  public function getScripts() {
    return $this->scripts;
  }

  public function packScripts() {

    $appPath = $this->appPath;
    $cachePrefix = 'cache/js';
    $cacheDir = $appPath->getPublicDir($cachePrefix);

    if(!is_dir($cacheDir)) mkdir($cacheDir, 0777, true);

    $scripts = $this->getScripts();
    $hash = sha1(implode('', $scripts));
    $cachedFilename = $hash.'.js';
    $localPath = $cachePrefix.DIRECTORY_SEPARATOR.$cachedFilename;
    $fullPath = $cacheDir.DIRECTORY_SEPARATOR.$cachedFilename;

    if(is_file($fullPath)) return $localPath;

    foreach($scripts as $sc) {
      $scriptContent = file_get_contents($appPath->getPublicDir($sc));
      file_put_contents($fullPath, $scriptContent.';', FILE_APPEND);
    }

    return $localPath;

  }

}
