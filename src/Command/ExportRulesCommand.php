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
use Symfony\Component\Yaml\Yaml;

class ExportRulesCommand extends Command
{

  protected $app;

  public function __construct(KarambolApp $app) {
    parent::__construct();
    $this->app = $app;
  }

  protected function configure()
  {
    $this
      ->setName('karambol:rules:export')
      ->setDescription('Export the rules from Karambol')
      ->addOption('ruleset', null, InputOption::VALUE_OPTIONAL, 'Only export the rules associated with this ruletset', null)
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {

    $orm = $this->app['orm'];
    $rulesetFilter = $input->getOption('ruleset');

    // Check that the filter actually matches an existing ruleset
    if($rulesetFilter !== null) {
      $rulesetExist = $this->rulesetExists($rulesetFilter);
      if(!$rulesetExist) {
        $output->writeln(sprintf('<error>The ruleset "%s" does not exists !', $rulesetFilter));
        return 1;
      }
    }

    $dump = [
      'version' => 1,
      'rules' => []
    ];

    $rules = $orm->getRepository(CustomRule::class)->findAll();
    foreach($rules as $rule) {
      $ruleset = $rule->getRuleset()->getName();
      if($rulesetFilter === null || $ruleset === $rulesetFilter) {
        $dump['rules'][] = [
          'origin' => $rule->getOrigin(),
          'set' => $rule->getRuleset()->getName(),
          'order' => $rule->getOrder(),
          'condition' => $rule->getCondition(),
          'actions' => $rule->getActions()
        ];
      }
    }

    $now = new \DateTime('now');
    echo sprintf('# Karambol rules export -- %s'.PHP_EOL, $now->format('Y-m-d H:i:s'));
    echo Yaml::dump($dump, 3, 2);

  }

  protected function rulesetExists($rulesetName) {

    $orm = $this->app['orm'];
    $qb = $orm->getRepository(RuleSet::class)->createQueryBuilder('r');

    $qb->select('count(r)')
      ->where($qb->expr()->eq('r.name', $qb->expr()->literal($rulesetName)))
    ;

    return $qb->getQuery()->getSingleScalarResult() == 1;

  }

}
