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
use Karambol\Entity\RuleSet;
use Karambol\Entity\CustomRule;
use Karambol\RuleEngine\RuleEngine;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class LoadRulesCommand extends Command
{

  protected $app;

  public function __construct(KarambolApp $app) {
    parent::__construct();
    $this->app = $app;
  }

  protected function configure() {
    $this
      ->setName('karambol:rules:load')
      ->addArgument('dumpPath', InputArgument::REQUIRED, 'The file containing the rules to import')
      ->addOption('cleanup', 'c', InputOption::VALUE_OPTIONAL, 'Cleanup the existing rules before import', null)
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
      $output->writeln('<info>Deleting existing rules...</info>');
      $numDeleted = $this->cleanupRules();
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
      $ruleset = $this->ensureRuleset($rule->getRuleset());
      $rule->setRuleset($ruleset);
      $orm->merge($rule);
    }
    $orm->flush();
  }

  protected function ensureRuleset(RuleSet $ruleset) {
    $orm = $this->app['orm'];
    $existingRuleset = $orm->getRepository(RuleSet::class)->findOneByName($ruleset->getName());
    if($existingRuleset) $ruleset->setId($existingRuleset->getId());
    return $ruleset;
  }

  protected function cleanup() {

  }

}
