<?php
namespace Karambol\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Karambol\KarambolApp;
use Cocur\Slugify\Slugify;

class CreatePluginCommand extends Command
{

  const PLUGIN_NAME_PREFIX = 'karambol-plugin-';
  const SCAFFOLD_SRC_DIR = __DIR__.'/../../resources/development/scaffold/plugin';

  protected $app;

  public function __construct(KarambolApp $app) {
    parent::__construct();
    $this->app = $app;
    $this->twig = $this->getTwigEnvironment();
  }

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
    mkdir($pluginDir.'/src/Views', 0700);
    mkdir($pluginDir.'/src/Controllers', 0700);
    mkdir($pluginDir.'/src/Entity', 0700);
    mkdir($pluginDir.'/locales', 0700);
    mkdir($pluginDir.'/public', 0700);

    $vars = [
      'pluginName' => $pluginName,
      'pluginNameSlug' => $pluginNameSlug,
      'pluginNamespace' => $pluginNamespace,
      'pluginNamespaceSlug' => $pluginNamespaceSlug,
    ];

    $this->copyTemplate('config.yml.twig', $pluginDir.'/config.yml', $vars);
    $this->copyTemplate('composer.json.twig', $pluginDir.'/composer.json', $vars);
    $this->copyTemplate('Plugin.php.twig', $pluginDir.'/src/'.$pluginName.'.php', $vars);

    $output->writeln('<info>Initializing Composer plugin\'s environment...</info>');

  }

  protected function copyTemplate($templateName, $dest, $vars = [])
  {
    $content = $this->twig->render($templateName, $vars);
    file_put_contents($dest, $content);
  }

  protected function getTwigEnvironment()
  {
    $loader = new \Twig_Loader_Filesystem(self::SCAFFOLD_SRC_DIR);
    return new \Twig_Environment($loader);
  }

}
