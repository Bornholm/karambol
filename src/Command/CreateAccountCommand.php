<?php

namespace Karambol\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Karambol\KarambolApp;
use Karambol\Entity\User;

class CreateAccountCommand extends Command
{

  protected $app;

  public function __construct(KarambolApp $app) {
    parent::__construct();
    $this->app = $app;
  }

  protected function configure()
  {
    $this
      ->setName('karambol:account:create')
      ->setDescription('Create a new account')
      ->addArgument(
        'username',
        InputArgument::REQUIRED,
        'The account\'s username'
      )
      ->addArgument(
        'password',
        InputArgument::REQUIRED,
        'The account\'s password'
      )
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {

    $username = $input->getArgument('username');
    $password = $input->getArgument('password');

    $accounts = $this->app['accounts'];
    $accounts->createAccount($username, $password);

    $output->writeln('<info>Account created.</info>');

  }

}
