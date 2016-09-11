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

    $helper = $this->getHelper('question');

    $question = new ConfirmationQuestion('<info>This will delete all your current rules. Are you sure ? (y/N)</info>', false);
    if (!$helper->ask($input, $output, $question)) {
      return 0;
    }

    $output->writeln('<comment>Reseting rules...</comment>');
    $this->truncateRules();

    $output->writeln('<comment>Seeding customization rules...</comment>');
    $this->seedCustomizationRules();

    $output->writeln('<comment>Seeding access control rules...</comment>');
    $this->seedAccessControlRules();

    $output->writeln('<info>Rules added.</info>');

  }

  protected function truncateRules() {
    $orm = $this->app['orm'];
    $cmd = $orm->getClassMetadata('Karambol\Entity\CustomRule');
    $connection = $orm->getConnection();
    $dbPlatform = $connection->getDatabasePlatform();
    $connection->query('SET FOREIGN_KEY_CHECKS=0');
    $q = $dbPlatform->getTruncateTableSql($cmd->getTableName());
    $connection->executeUpdate($q);
    $connection->query('SET FOREIGN_KEY_CHECKS=1');
  }

  protected function seedCustomizationRules() {

    $rules = [
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

    $this->seedRules(RuleEngine::CUSTOMIZATION, $rules);

  }

  protected function seedAccessControlRules() {

    $rules = [
      [
        'condition' => 'true',
        'actions' => [
          'allow("access", "url[/, /login, /register, /doc*, /p/home]")'
        ]
      ],
      [
        'condition' => 'isConnected()',
        'actions' => [
          'allow("access", "url[/profile]")'
        ]
      ],
      [
        'condition' => 'owns(resource)',
        'actions' => [
          'allow("*", resource)'
        ]
      ]
    ];

    $this->seedRules(RuleEngine::ACCESS_CONTROL, $rules);

  }

  protected function seedRules($rulesetName, $rules) {

    $orm = $this->app['orm'];

    $ruleset = $orm->getRepository('Karambol\Entity\RuleSet')
      ->findOneByName($rulesetName)
    ;

    if(!$ruleset) {
      $ruleset = new RuleSet();
      $ruleset->setName($rulesetName);
      $orm->persist($ruleset);
      $orm->flush();
    }

    foreach($rules as $i => $ruleData) {
      $rule = new CustomRule();
      $rule->setCondition($ruleData['condition']);
      $rule->setAction(implode(PHP_EOL, $ruleData['actions']));
      $rule->setOrder($i);
      $rule->setRuleset($ruleset);
      $rule->setOrigin(CustomRule::ORIGIN_SEED);
      $orm->persist($rule);
    }

    $orm->flush();

  }

}
