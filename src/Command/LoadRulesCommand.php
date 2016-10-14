<?php

namespace Karambol\Command;

use Karambol\RuleEngine\Backup\Deserializer;
use Karambol\RuleEngine\Backup\Transform\CustomRuleTransformer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Karambol\KarambolApp;
use Karambol\Entity\Ruleset;
use Karambol\Entity\CustomRule;
use Karambol\RuleEngine\RuleEngine;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class LoadRulesCommand extends Command
{

  const ALL_RULESETS = 'all';

  protected $app;

  public function __construct(KarambolApp $app) {
    parent::__construct();
    $this->app = $app;
  }

  protected function configure() {
    $this
      ->setName('karambol:rules:load')
      ->addArgument('dumpPath', InputArgument::REQUIRED, 'The file containing the rules to import')
      ->addOption('cleanup', 'c', InputOption::VALUE_OPTIONAL, 'Cleanup the specified ruleset before import, or "all" to cleanup all rulesets', null)
      ->setDescription('Load a set of rules from a file')
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output) {

    $dumpPath = $input->getArgument('dumpPath');
    $cleanup = $input->getOption('cleanup');

    if(!is_file($dumpPath)) {
      $output->writeln(sprintf('<error>Cannot find the file "%s" !</error>', $dumpPath));
      return 1;
    }

    $dumpStr = file_get_contents($dumpPath);

    $deserializer = new Deserializer();
    $transformer = new CustomRuleTransformer();

    $rules = $deserializer->deserialize($dumpStr, $transformer);

    if($cleanup !== null) {

      $allRulesets = $cleanup === self::ALL_RULESETS;

      if(!$allRulesets && !$this->rulesetExists($cleanup)) {
        $output->writeln(sprintf('<error>The ruleset "%s" does not exist !</error>', $cleanup));
        return 1;
      }

      $output->writeln(sprintf('<info>Deleting existing rules%s...</info>',
        $allRulesets ? '' : ' for ruleset "'.$cleanup.'"'
      ));

      $numDeleted = $this->cleanupRules($cleanup);

      $output->writeln(sprintf('<info>Deleted %s rules.</info>', $numDeleted));

    }

    $output->writeln(sprintf('<info>Importing rules from "%s"...</info>', realpath($dumpPath)));
    $this->loadRules($rules);

    $output->writeln('<info>Done.</info>');

  }

  protected function cleanupRules() {
    $orm = $this->app['orm'];
    $qb = $orm->getRepository(CustomRule::class)->createQueryBuilder('r');
    $qb->delete();
    return $qb->getQuery()->getSingleScalarResult();
  }

  protected function loadRules(array $rules) {
    $orm = $this->app['orm'];
    foreach($rules as $rule) {
      $ruleset = $this->mergeExistingRuleset($rule->getRuleset());
      $rule->setRuleset($ruleset);
      $orm->merge($rule);
    }
    $orm->flush();
  }

  protected function mergeExistingRuleset(Ruleset $ruleset) {
    $orm = $this->app['orm'];
    $existingRuleset = $orm->getRepository(Ruleset::class)->findOneByName($ruleset->getName());
    if($existingRuleset) $ruleset->setId($existingRuleset->getId());
    return $ruleset;
  }

  protected function cleanup($cleanupRuleset) {
    $orm = $this->app['orm'];
    $qb = $orm->getRepository(CustomRule::class)->createQueryBuilder('r');
    $qb->delete();
    if( $cleanupRuleset !== self::ALL_RULESETS ) {
      $qb->join('r.ruleset', 's');
      $qb->where($qb->expr()->eq('s.name', $qb->expr()->literal($cleanupRuleset)));
    }
    return $qb->getQuery()->getSingleScalarResult();
  }

  protected function rulesetExists($rulesetName) {
    $orm = $this->app['orm'];
    return $orm->getRepository(Ruleset::class)->exists($rulesetName);
  }

}
