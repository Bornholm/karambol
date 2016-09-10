<?php
/**
 *
 * PluginConfigCommand
 *
 * Permet de copier le fichier de configuration d'un plugin
 * dans le répertoire config/local.d/
 *
 * Utilisation:
 *     - script/console karambol:plugin:config namespace/pluginName
 * Exemple pour un plugin foo/karambol-plugin-test:
 *    - script/console karambol:plugin:config foo/test
 *
 * @author Benjamin Gaudé <development@gaudebenjamin.com>
 * @license AGPLv3
 *
 */
namespace Karambol\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Karambol\KarambolApp;
use Karambol\Account\Exception\AccountExistsException;

class PluginConfigCommand extends Command
{

  protected $app;

  public function __construct(KarambolApp $app) {
    parent::__construct();
    $this->app = $app;
  }

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
