<?php

namespace Karambol\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Karambol\KarambolApp;

class LinkAssetsCommand extends Command
{

  protected $app;

  public function __construct(KarambolApp $app) {
    parent::__construct();
    $this->app = $app;
  }

  protected function configure()
  {
    $this
      ->setName('karambol:assets:link')
      ->setDescription('Expose the registered assets in the public web directory.')
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {

    $assets = $this->app['config']['assets'];
    $appPath = $this->app['app_path'];
    $baseDir = $appPath->getAppDirectory();

    foreach($assets as $assetsNamespace) {
      foreach($assetsNamespace as $assetItem) {

        $src = $baseDir.DIRECTORY_SEPARATOR.$assetItem['src'];
        $dest = $baseDir.DIRECTORY_SEPARATOR.$assetItem['dest'];

        if(!file_exists($src)) {
          $output->writeln(sprintf('<error>Source path "%s" does not exists !</error>', $src));
          continue;
        }

        if(!is_dir($src)) {
          $output->writeln(sprintf('<error>Source path "%s" must be a valid directory !</error>', $src));
          continue;
        }

        if(!file_exists($dest)) {
          $destParentDir = dirname($dest);
          if(!file_exists($destParentDir)) mkdir($destParentDir, 0777, true);
          $output->writeln(sprintf('<info>Linking assets "%s" to "%s"</info>', $src, $dest));
        } else {
          $output->writeln(sprintf('<info>Relinking assets "%s" to "%s"</info>', $src, $dest));
          unlink($dest);
        }

        $this->createLink($src, $dest);

      }
    }

  }

  protected function createLink($src, $dest) {
    if(!is_link($dest) && !file_exists($dest)) {
      symlink($src, $dest);
    }
  }
}
