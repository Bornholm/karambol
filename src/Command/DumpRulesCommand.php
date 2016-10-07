<?php

namespace Karambol\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Karambol\KarambolApp;
use Karambol\Entity\RuleSet;

class DumpRulesCommand extends Command
{

  protected $app;

  public function __construct(KarambolApp $app) {
    parent::__construct();
    $this->app = $app;
  }

  protected function configure()
  {
    $this
      ->setName('karambol:rules:dump')
      ->setDescription('Export the rules from Karambol')
      ->addOption('ruleset', 'r', InputOption::VALUE_OPTIONAL, 'Only export the rules associated with this ruleset', null)
      ->addOption('origin', 'o', InputOption::VALUE_OPTIONAL, 'Only export the rules associated with this origin', null)
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {

    $rulesetFilter = $input->getOption('ruleset');
    $originFilter = $input->getOption('origin');

    // Check that the filter actually matches an existing ruleset
    if($rulesetFilter !== null) {
      $rulesetExist = $this->rulesetExists($rulesetFilter);
      if(!$rulesetExist) {
        $output->writeln(sprintf('<error>The ruleset "%s" does not exists !', $rulesetFilter));
        return 1;
      }
    }

    $ruleDumper = $this->app['rule_dumper'];

    echo $ruleDumper->dump($rulesetFilter, $originFilter);

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
