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
    $logger = $app['monolog'];
    $baseDir = __DIR__.'/../..';

    $logger->info('Linking assets...');

    foreach($assets as $assetsNamespace) {
      foreach($assetsNamespace as $assetItem) {

        $src = $baseDir.'/'.$assetItem['src'];
        $dest = $baseDir.'/'.$assetItem['dest'];

        if(!file_exists($src)) $logger->warn(sprintf('Asset "%s" does not exists !', $src));
        if(!is_dir($src)) $logger->warn(sprintf('Asset "%s" must be a directory !', $src));

        if( is_dir($src) && !file_exists($dest)) {
          $destParentDir = dirname($dest);
          if(!file_exists($destParentDir)) mkdir($destParentDir, 0777, true);
          symlink($src, $dest);
        }

      }
    }

  }

}
