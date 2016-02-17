<?php

namespace Karambol\Script;

use Karambol\KarambolApp;
use Symfony\Component\Yaml\Yaml;

class AssetsLinker
{

  public static function linkAssets() {
    $app = new KarambolApp();
    self::linkVendorAssets($app);
  }

  public static function linkVendorAssets(KarambolApp $app) {

    $assets = $app['config']['assets'];

    $baseDir = __DIR__.'/../';

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
