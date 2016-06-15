<?php

namespace Karambol\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Karambol\KarambolApp;
use Karambol\Entity\User;
use Karambol\Entity\RuleSet;
use Karambol\Entity\CustomRule;
use Karambol\RuleEngine\RuleEngineService;

class PromoteUserCommand extends Command
{

  protected $app;

  public function __construct(KarambolApp $app) {
    parent::__construct();
    $this->app = $app;
  }

  protected function configure()
  {
    $this
      ->setName('karambol:user:promote')
      ->setDescription('Promote a registered user to admin.')
      ->addArgument(
        'email',
        InputArgument::REQUIRED,
        'The user\'s email'
      )
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {

    $email = $input->getArgument('email');
    $orm = $this->app['orm'];

    $user = $orm->getRepository('Karambol\Entity\User')->findOneByEmail($email);


    if(!$user) {
      $output->writeln(sprintf('<error>User "%s" does not exists.</error>', $email));
      exit(1);
    }

    $rule = new CustomRule();
    $rule->setCondition(sprintf('user.id == %s', $user->getId()));
    $rule->setAction('addRole("ROLE_ADMIN")');

    $ruleset = $orm->getRepository('Karambol\Entity\RuleSet')->findOneByName(RuleEngineService::ACCESS_CONTROL);
    if(!$ruleset) {
      $ruleset = new RuleSet();
      $ruleset->setName(RuleEngineService::ACCESS_CONTROL);
      $orm->persist($ruleset);
      $orm->flush();
    }
    $rule->setRuleset($ruleset);

    $orm->persist($rule);
    $orm->flush();

    $output->writeln('<info>Account promoted.</info>');

  }

}
