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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Karambol\KarambolApp;
use Karambol\Account\Exception\AccountExistsException;

/**
 * Commande configuration plugin
 * @package Karambol
 * @since 1.0.0
 * @license AGPLv3
 * @author Benjamin Gaudé
 */
class PluginConfigCommand extends Command
{
  /**
   * Application
   * @var KarambolApp 
   * @author Benjamin Gaudé
   */
  protected $app;
  
  /**
   * Constructeur de classe
   * @param KarambolApp $app
   * @author Benjamin Gaudé
   */
  public function __construct(KarambolApp $app) {
    parent::__construct();
    $this->app = $app;
  }
  
  /**
   * Configure command
   * @author Benjamin Gaudé
   */
  protected function configure()
  {
    $this
      ->setName('karambol:plugin:config')
      ->setDescription('Expose the plugin configuration file.')
      ->addArgument(
        'name',
        InputArgument::REQUIRED,
        'The plugin name'
      )
    ;
  }
  
  /**
   * Execute la commande
   * @param InputInterface $input
   * @param OutputInterface $output
   * @return int
   * @author Benjamin Gaudé
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $prefixPlugin = 'karambol-plugin-';
    $basePath = dirname(dirname(__DIR__));
    $vendoPath = "vendor/";
    $distPath = $basePath.'/config/local.d/';

    list($namespace,$plugin) = explode('/', $input->getArgument('name'));

    $configPath = $basePath.'/'.$vendoPath.$namespace.'/'.$prefixPlugin.$plugin.'/config/'.$plugin.'.yml';

    $output->writeln('<info>Copy '.$configPath.' to '.$distPath.'</info>');

    if(is_file($configPath)) {
        if(is_writable($distPath)) {
            copy($configPath, $distPath.$plugin.'.yml');
        } else {
            $output->writeln('<error>'.$configPath.' is not writable.</error>');
        }
    } else {
        $output->writeln('<error>Unable to find '.$configPath.'</error>');
    }

  }

}
