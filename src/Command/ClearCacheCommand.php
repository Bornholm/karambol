<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Karambol\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Karambol\KarambolApp;

/**
 * Commande nettoyage cache
 * @package Karambol
 * @since 1.0.0
 * @license AGPLv3
 * @author William Petit
 */
class ClearCacheCommand extends Command
{
  /**
   * Droits fichiers
   * @var int
   */
  const FILE_MODE = 0774;
  
  /**
   * Application
   * @var KarambolApp 
   * @author William Petit
   */
  protected $app;
  
  /**
   * Constructeur de classe
   * @param KarambolApp $app
   * @author William Petit
   */
  public function __construct(KarambolApp $app) {
    parent::__construct();
    $this->app = $app;
  }
  
  /**
   * Configure command
   * @author William Petit
   */
  protected function configure() {
    $this
      ->setName('karambol:cache:clear')
      ->setDescription('Clear the application cache')
    ;
  }
  
  /**
   * Execute la commande
   * @param InputInterface $input
   * @param OutputInterface $output
   * @return int
   * @author William Petit
   */
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
  
  /**
   * Nettoie un repertoire de manière recursive
   * @param type $dirPath
   * @author William Petit
   */
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
