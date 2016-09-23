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

  protected function configure()
  {
    $this
      ->setName('karambol:rules:load')
      ->addArgument(
        'rules',
        InputArgument::REQUIRED,
        'The file containing the rules to load'
      )
      ->setDescription('Load a set of rules from a file')
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    
  }

}
