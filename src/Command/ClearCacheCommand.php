<?php

namespace Karambol\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Karambol\KarambolApp;

class ClearCacheCommand extends Command
{

  const FILE_MODE = 0774;

  protected $app;

  public function __construct(KarambolApp $app) {
    parent::__construct();
    $this->app = $app;
  }

  protected function configure() {
    $this
      ->setName('karambol:cache:clear')
      ->setDescription('Clear the application cache')
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output) {

    $appPath = $this->app['app_path'];

    $publicCacheDir = $appPath->getPublicCacheDir();

    if(!is_dir($publicCacheDir) && is_file($publicCacheDir)) {
      $output->writeln(sprintf('<error>Error: "%s" should be a directory !</error>', $publicCacheDir));
      return 1;
    }

    if(!file_exists($publicCacheDir)) mkdir($publicCacheDir, self::FILE_MODE, true);

    $output->writeln(sprintf('<info>Cleaning up public cache (%s)...</info>', $publicCacheDir));
    $this->cleanDirRecursively($publicCacheDir);

    $cacheDir = $appPath->getCacheDir();

    if(!is_dir($cacheDir) && is_file($cacheDir)) {
      $output->writeln(sprintf('<error>Error: "%s" should be a directory !</error>', $cacheDir));
      return 1;
    }

    if(!file_exists($cacheDir)) mkdir($cacheDir, self::FILE_MODE, true);

    $output->writeln(sprintf('<info>Cleaning up cache (%s)...</info>', $cacheDir));
    $this->cleanDirRecursively($cacheDir);

    $output->writeln('<info>Done.</info>');

  }

  protected function cleanDirRecursively($dirPath) {
    $files = scandir($dirPath);
    foreach($files as $fileName) {
      if($fileName === '.' || $fileName === '..') continue;
      $filePath = $dirPath.DIRECTORY_SEPARATOR.$fileName;
      if(is_dir($filePath)) {
        if(is_link($filePath)) {
          unlink($filePath);
        } else {
          $this->cleanDirRecursively($filePath);
          rmdir($filePath);
        }
        continue;
      }
      if(is_file($filePath)) unlink($filePath);
    }
  }

}
