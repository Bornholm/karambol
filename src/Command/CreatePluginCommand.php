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
use Symfony\Component\Console\Output\OutputInterface;
use Karambol\KarambolApp;
use Cocur\Slugify\Slugify;

/**
 * Commande creation de plugin
 * @package Karambol
 * @license AGPLv3
 * @author William Petit
 */
class CreatePluginCommand extends Command
{
  /**
   * Prefix plugin
   * @var string
   */
  const PLUGIN_NAME_PREFIX = 'karambol-plugin-';
  
  /**
   * Scaffold dir
   * @var string
   */
  const SCAFFOLD_SRC_DIR = 'resources/development/scaffold/plugin';
  
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
    $this->twig = $this->getTwigEnvironment();
  }
  
  /**
   * Configure command
   * @author William Petit
   */
  protected function configure()
  {
    $this
      ->setName('karambol:plugin:create')
      ->setDescription('Bootstrap a new plugin and register in Karambol')
      ->addArgument(
        'pluginFullName',
        InputArgument::REQUIRED,
        'The plugin\'s namespace and name. Should be in the form "MyNamespace/MyPluginName"'
      )
      ->addArgument(
        'pluginParentDir',
        InputArgument::REQUIRED,
        'Destination directory'
      )
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

    $pluginFullName = $input->getArgument('pluginFullName');
    $pluginParentDir = $input->getArgument('pluginParentDir');

    $matches = [];
    if(!preg_match('/^(([A-Z][a-z0-9]+)+)\/(([A-Z][a-z0-9]+)+)$/', $pluginFullName, $matches)) {
      $output->writeln(sprintf('<error>You plugin\'s name must be in the form "MyNamespace/MyPluginName" !</error>'));
      return 1;
    }

    $pluginNamespace = $matches[1];
    $pluginName = $matches[3];

    $slugify = new Slugify();
    $pluginNameSlug = $slugify->slugify($pluginName);
    $pluginNamespaceSlug = $slugify->slugify($pluginNamespace);

    $output->writeln('<info>Verifying destination directory...</info>');

    if(!is_dir($pluginParentDir)) {
      $output->writeln(sprintf('<error>The directory "%s" does not exist !</error>', $pluginParentDir));
      return 1;
    }

    $pluginDir = $pluginParentDir.DIRECTORY_SEPARATOR.self::PLUGIN_NAME_PREFIX.$pluginNameSlug;

    if(file_exists($pluginDir)) {
      $output->writeln(sprintf('<error>A file or directory already exists at path "%s" !</error>', $pluginDir));
      return 1;
    }

    $output->writeln(sprintf('<info>Creating plugin directory tree "%s"...</info>', $pluginDir));

    mkdir($pluginDir, 0700);
    mkdir($pluginDir.'/src', 0700);
    mkdir($pluginDir.'/src/Views/plugins/'.$pluginNamespaceSlug.'/'.$pluginNameSlug, 0700, true);
    mkdir($pluginDir.'/src/Controller', 0700);
    mkdir($pluginDir.'/src/Entity', 0700);
    mkdir($pluginDir.'/locales', 0700);
    mkdir($pluginDir.'/public', 0700);

    $developerName = $this->findDeveloperName($output);
    $developerEmail = $this->findDeveloperEmail($output);

    $vars = [
      'pluginName' => $pluginName,
      'pluginNameSlug' => $pluginNameSlug,
      'pluginNamespace' => $pluginNamespace,
      'pluginNamespaceSlug' => $pluginNamespaceSlug,
      'developerEmail' => $developerEmail,
      'developerName' => $developerName,
    ];

    $this->copyTemplate('config.yml.twig', $pluginDir.'/config.yml', $vars);
    $this->copyTemplate('composer.json.twig', $pluginDir.'/composer.json', $vars);
    $this->copyTemplate('Plugin.php.twig', $pluginDir.'/src/'.$pluginName.'Plugin.php', $vars);
    $this->copyTemplate('gitignore.twig', $pluginDir.'/.gitignore', $vars);
    $this->copyTemplate('fr.yml.twig', $pluginDir.'/locales/fr.yml', $vars);
    $this->copyTemplate('MyController.php.twig', $pluginDir.'/src/Controller/MyController.php', $vars);
    $this->copyTemplate('mypage.html.twig', $pluginDir.'/src/Views/plugins/'.$pluginNamespaceSlug.'/'.$pluginNameSlug.'/mypage.html.twig', $vars);

    $output->writeln('<info>Initializing plugin\'s environment...</info>');

    $appDir = $this->app['app_path']->getAppDirectory();
    $exitCode = passthru('PWD="'.$appDir.'" ./composer -n --working-dir="'.$pluginDir.'" install');

    if($exitCode != 0) {
      $output->writeln('<error>An unknown error was raised during the Composer execution. Abort.</error>');
      return $exitCode;
    }

    $output->writeln('<info>Linking the new plugin to your Karambol instance...</info>');

    // Linking plugin configuration to local Karambol
    $pluginConfigLink = $appDir.'/config/local.d/'.$pluginNameSlug.'.yml';

    if(file_exists($pluginConfigLink)) {
      $output->writeln(sprintf('<error>The configuration file "%s" already exists ! Please remove it and retry.</error>', $pluginConfigLink));
      return 1;
    }
    symlink($pluginDir.'/config.yml', $pluginConfigLink);

    // Add the plugin to local dependencies
    $composerManifestPath = $appDir.'/composer.local.json';
    $composerPluginName = $pluginNamespaceSlug.'/'.$pluginNameSlug;

    if(is_file($composerManifestPath)) {
      $manifest = json_decode(file_get_contents($composerManifestPath), true);
      if(!$manifest) {
        $output->writeln(sprintf('<error>The local composer manifest "%s" seems to be malformed ! Aborting.</error>', $composerManifestPath));
        return 1;
      }
    } else {
      $manifest = [
        'repositories' => [],
        'require' => []
      ];
    }

    // Search for existing repositories linked to the plugin directory
    $matchingRepositories = array_filter($manifest['repositories'], function($repo) use ($pluginDir) {
      return $repo['url'] === $pluginDir;
    });

    if(count($matchingRepositories) === 0) {
      $manifest['repositories'][] = [
        'type' => 'path',
        'url' => $pluginDir
      ];
    }

    $manifest['require'][$composerPluginName] = '@dev';

    file_put_contents($composerManifestPath, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

    $output->writeln('<info>Updating Karambol dependencies...</info>');

    $exitCode = passthru('PWD="'.$appDir.'" ./composer -n update "'.$composerPluginName.'"');

    if($exitCode != 0) {
      $output->writeln('<error>An unknown error was raised during the Composer execution. Aborting.</error>');
      return $exitCode;
    }

    $exitCode = passthru('PWD="'.$appDir.'" ./bin/cli karambol:cache:clear');
    if($exitCode != 0) {
      $output->writeln('<error>An unknown error was raised  while clearing cache. Aborting.</error>');
      return $exitCode;
    }

    $exitCode = passthru('PWD="'.$appDir.'" ./bin/cli karambol:assets:link');
    if($exitCode != 0) {
      $output->writeln('<error>An unknown error was raised while linking assets. Aborting.</error>');
      return $exitCode;
    }

    $output->writeln('<info>Your plugin is created. You can now connect to the administration panel et activate it in the configuration section.</info>');

  }
  
  /**
   * Copie du template
   * @param string $templateName
   * @param string $dest
   * @param array $vars
   * @author William Petit
   */
  protected function copyTemplate($templateName, $dest, $vars = [])
  {
    $content = $this->twig->render($templateName, $vars);
    file_put_contents($dest, $content);
  }
  
  /**
   * Renvoi l'environnement twig
   * @return \Twig_Environment
   * @author William Petit
   */
  protected function getTwigEnvironment()
  {
    $appDir = $this->app['app_path']->getAppDirectory();
    $loader = new \Twig_Loader_Filesystem($appDir.'/'.self::SCAFFOLD_SRC_DIR);
    return new \Twig_Environment($loader);
  }
  
  /**
   * Renvoi le nom du developpeur
   * @param OutputInterface $output
   * @return string
   * @author William Petit
   */
  protected function findDeveloperName($output)
  {
    $name = shell_exec('git config --global --get user.name');
    if($name !== null) return trim($name, " \t\n\r");
    while(empty($name = readline('[?] Please enter your developer\'s name: '))) {
      $output->writeln('<info>Please enter a non empty name.</info>');
    }
    return trim($name, " \t\n\r");
  }
  
  /**
   * Renvoi l'adresse email
   * @param OutputInterface $output
   * @return string
   * @author William Petit
   */
  protected function findDeveloperEmail($output)
  {
    $email = shell_exec('git config --global --get user.email');
    if($email !== null) return trim($email, " \t\n\r");
    while(empty($email = readline('[?] Please enter your developer\'s email adress: '))) {
      $output->writeln('<info>Please enter a non empty email adress.</info>');
    }
    return trim($email, " \t\n\r");
  }

}
