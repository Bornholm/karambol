<?php

namespace Karambol\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Karambol\KarambolApp;
use Karambol\Entity\RuleSet;
use Karambol\Entity\CustomRule;
use Karambol\RuleEngine\RuleEngine;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class LoadRulesCommand extends Command
{

  protected $app;

  public function __construct(KarambolApp $app) {
    parent::__construct();
    $this->app = $app;
  }

  protected function configure() {
    $this
      ->setName('karambol:rules:load')
      ->addArgument('dumpPath', InputArgument::REQUIRED, 'The file containing the rules to import')
      ->addOption('cleanup', 'c', InputOption::VALUE_NONE, 'Cleanup the existing rules before import')
      ->setDescription('Load a set of rules from a file')
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output) {

    $dumpPath = $input->getArgument('dumpPath');
    $cleanup = $input->getOption('cleanup');

    if(!is_file($dumpPath)) {
      $output->writeln(sprintf('<error>Canno\'t find the file "%s" !</error>', $dumpPath));
      return 1;
    }

    $output->writeln(sprintf('<info>Importing rules from "%s"...</info>', realpath($dumpPath)));

    $dumpStr = file_get_contents($dumpPath);

    $ruleDumper = $this->app['rule_dumper'];

    $ruleDumper->load($dumpStr, $cleanup);

    $output->writeln('<info>Done.</info>');

  }

}
