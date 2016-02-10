<?php

namespace Karambol\Script;

use Symfony\Component\Yaml\Yaml;

class AssetsLinker
{

  public static function linkAssets() {

    $baseDir = __DIR__.'/../..';
    $assetsFile = $baseDir.'/config/assets.yml';
    $assetsConfig = Yaml::parse(file_get_contents($assetsFile));

    $assets = $assetsConfig['assets'];

    foreach($assets as $assetItem) {

      $src = $baseDir.'/'.$assetItem['src'];
      $dest = $baseDir.'/'.$assetItem['dest'];

      if( is_dir($src) && !file_exists($dest)) {
        $destParentDir = dirname($dest);
        if(!file_exists($destParentDir)) mkdir($destParentDir, 0777, true);
        symlink($src, $dest);
      }

    }

  }

}