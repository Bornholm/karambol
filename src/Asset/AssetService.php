<?php

namespace Karambol\Asset;

class AssetService {

  protected $scripts = [];
  protected $stylesheets = [];
  protected $publicDir;

  public function __construct($publicDir) {
    $this->publicDir = $publicDir;
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

    $cachePrefix = 'cache/js';
    $cacheDir = $this->publicDir.'/'.$cachePrefix;

    if(!is_dir($cacheDir)) mkdir($cacheDir, 0777, true);

    $scripts = $this->getScripts();
    $hash = sha1(implode('', $scripts));
    $cachedFilename = $hash.'.js';
    $localPath = $cachePrefix.'/'.$cachedFilename;
    $fullPath = $cacheDir.'/'.$cachedFilename;

    if(is_file($fullPath)) return $localPath;

    foreach($scripts as $sc) {
      $scriptContent = file_get_contents($this->publicDir.'/'.$sc);
      file_put_contents($fullPath, $scriptContent.';', FILE_APPEND);
    }

    return $localPath;

  }

}
