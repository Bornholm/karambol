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
 * Commande generation des assets
 * @package Karambol
 * @since 1.0.0
 * @license AGPLv3
 * @author William Petit
 */
class LinkAssetsCommand extends Command
{
  
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
  protected function configure()
  {
    $this
      ->setName('karambol:assets:link')
      ->setDescription('Expose the registered assets in the public web directory.')
    ;
  }
  
  /**
   * Execute la commande
   * @param InputInterface $input
   * @param OutputInterface $output
   * @return int
   * @author William Petit
   */
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
