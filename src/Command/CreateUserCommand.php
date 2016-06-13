<?php

namespace Karambol\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Karambol\KarambolApp;
use Symfony\Component\Console\Question\Question;

class CreateUserCommand extends Command
{

  protected $app;

  public function __construct(KarambolApp $app) {
    parent::__construct();
    $this->app = $app;
  }

  protected function configure()
  {
    $this
      ->setName('karambol:user:create')
      ->setDescription('Create a new user')
      ->addArgument(
        'email',
        InputArgument::REQUIRED,
        'The user\'s email'
      )
      ->addArgument(
        'password',
        InputArgument::REQUIRED,
        'The user\'s password'
      )
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $email = $input->getArgument('email');
    $password = $input->getArgument('password');
  }

}
