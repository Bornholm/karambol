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
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {

    $translator = $this->app['translator'];
    $helper = $this->getHelper('question');

    $question = new Question($translator->trans('commands.user.create.enter_user_email'), false);
    $email = $helper->ask($input, $output, $question);

    $question = new Question($translator->trans('commands.user.create.enter_user_password'), false);
    $question->setHidden(true);
    $password = $helper->ask($input, $output, $question);

    $question = new Question($translator->trans('commands.user.create.confirm_user_password'), false);
    $question->setHidden(true);
    $passwordConfirm = $helper->ask($input, $output, $question);

  }

}
