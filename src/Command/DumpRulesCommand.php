<?php

namespace Karambol\Command;

use Karambol\Entity\CustomRule;
use Karambol\RuleEngine\Backup\Serializer;
use Karambol\RuleEngine\Backup\Transform\CustomRuleTransformer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Karambol\KarambolApp;
use Karambol\Entity\Ruleset;

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

    $serializer = new Serializer();

    $rules = $this->fetchRules($rulesetFilter, $originFilter);
    $serializer->addRules($rules);

    $transformer = new CustomRuleTransformer();
    echo $serializer->serialize($transformer);

  }

  protected function rulesetExists($rulesetName) {
    $orm = $this->app['orm'];
    return $orm->getRepository(Ruleset::class)->exists($rulesetName);
  }

  protected function fetchRules($rulesetFilter = null, $originFilter = null) {

    $orm = $this->app['orm'];
    $qb = $orm->getRepository(CustomRule::class)->createQueryBuilder('r');

    $qb->join('r.ruleset', 's');

    if($rulesetFilter !== null) {
      $qb->andWhere($qb->expr()->eq('s.name', $qb->expr()->literal($rulesetFilter)));
    }

    if($originFilter !== null) {
      $qb->andWhere($qb->expr()->eq('r.origin', $qb->expr()->literal($originFilter)));
    }

    return $qb->getQuery()->getResult();

  }



}
