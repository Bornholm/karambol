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
      ->setName('karambol:link-assets')
      ->setDescription('Expose the registered assets in the public web directory.')
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {

    $assets = $this->app['config']['assets'];
    $baseDir = __DIR__.'/../..';

    $output->writeln('<info>Linking assets...</info>');

    foreach($assets as $assetsNamespace) {
      foreach($assetsNamespace as $assetItem) {

        $src = $baseDir.'/'.$assetItem['src'];
        $dest = $baseDir.'/'.$assetItem['dest'];

        if(!file_exists($src)) $output->writeln(sprintf('Asset "%s" does not exists !', $src));
        if(!is_dir($src)) $output->writeln(sprintf('Asset "%s" must be a directory !', $src));

        if( is_dir($src) && !file_exists($dest)) {
          $destParentDir = dirname($dest);
          if(!file_exists($destParentDir)) mkdir($destParentDir, 0777, true);
          if(!is_link($dest) && !file_exists($dest)) symlink($src, $dest);
        }

      }
    }

  }
}
