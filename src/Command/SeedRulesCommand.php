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
use Karambol\RuleEngine\RuleEngineService;

class SeedRulesCommand extends Command
{

  protected $app;

  public function __construct(KarambolApp $app) {
    parent::__construct();
    $this->app = $app;
  }

  protected function configure()
  {
    $this
      ->setName('karambol:rules:seed')
      ->setDescription('Add set of default rules to bootstrap your application')
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {

    $orm = $this->app['orm'];

    $customizationRules = [
      [
        'condition' => 'not isConnected()',
        'actions' => ['addPageToMenu("login", "home_main", {"align":"right", "icon_class": "fa fa-sign-in"})']
      ],
      [
        'condition' => 'isGranted("ROLE_ADMIN")',
        'actions' => ['addPageToMenu("admin", "home_main", {"align":"right", "icon_class": "fa fa-wrench"})']
      ],
      [
        'condition' => 'isConnected()',
        'actions' => [
          'addPageToMenu("profile", "home_main", {"align":"right", "icon_class": "fa fa-user"})',
          'addPageToMenu("logout", "home_main", {"align":"right", "icon_class": "fa fa-sign-out"})'
        ]
      ],
    ];

    $customizationRuleset = $orm->getRepository('Karambol\Entity\RuleSet')->findOneByName(RuleEngineService::CUSTOMIZATION);

    if(!$customizationRuleset) {
      $customizationRuleset = new RuleSet();
      $customizationRuleset->setName(RuleEngineService::CUSTOMIZATION);
      $orm->persist($customizationRuleset);
      $orm->flush();
    }

    foreach($customizationRules as $ruleData) {
      $rule = new CustomRule();
      $rule->setCondition($ruleData['condition']);
      $rule->setAction(implode(PHP_EOL, $ruleData['actions']));
      $rule->setRuleset($customizationRuleset);
      $orm->persist($rule);
    }

    $orm->flush();

    $output->writeln('<info>Rules added.</info>');

  }

}
