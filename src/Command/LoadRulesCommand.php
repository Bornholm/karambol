<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
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

/**
 * Commande chargement des regles
 * @package Karambol
 * @license AGPLv3
 * @author William Petit
 */
class LoadRulesCommand extends Command
{
  /**
   * Toutes les règles
   * @var string
   */
  const ALL_RULESETS = 'all';
  
  /**
   * Application
   * @var KarambolApp 
   * @author William Petit
   */
  protected $app;

  /**
   * Constructeur de classe
   * @param KarambolApp $app
   * @author William Petit
   */
  public function __construct(KarambolApp $app) {
    parent::__construct();
    $this->app = $app;
  }
  
  /**
   * Configure command
   * @author William Petit
   */
  protected function configure() {
    $this
      ->setName('karambol:rules:load')
      ->addArgument('dumpPath', InputArgument::REQUIRED, 'The file containing the rules to import')
      ->addOption('cleanup', 'c', InputOption::VALUE_OPTIONAL, 'Cleanup the specified ruleset before import, or "all" to cleanup all rulesets', null)
      ->setDescription('Load a set of rules from a file')
    ;
  }
  
  /**
   * Execute la commande
   * @param InputInterface $input
   * @param OutputInterface $output
   * @return int
   * @author William Petit
   */
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

      if(!$allRulesets && !$this->rulesetExists($cleanup) ) {
        $output->writeln(sprintf('<error>The ruleset "%s" does not exist !</error>', $cleanup));
        return 1;
      }

      $output->writeln(sprintf('<info>Deleting existing rules%s...</info>',
        $allRulesets ? '' : ' for ruleset "'.$cleanup.'"'
      ));

      $numDeleted = $this->cleanup($cleanup);

      $output->writeln(sprintf('<info>Deleted %s rules.</info>', $numDeleted));

    }

    $output->writeln(sprintf('<info>Importing rules from "%s"...</info>', realpath($dumpPath)));
    $this->loadRules($rules);

    $output->writeln('<info>Done.</info>');

  }
  
  /**
   * Charge les regles
   * @param array $rules
   * @author William Petit
   */
  protected function loadRules(array $rules) {

    $orm = $this->app['orm'];

    $rulesets = [];
    foreach($rules as $rule) {
      $rulesetName = $rule->getRuleset()->getName();
      if(isset($rulesets[$rulesetName])) continue;
      $rulesetId = $this->ensureRuleset($rulesetName);
      $rulesets[$rulesetName] = $orm->getReference(Ruleset::class, $rulesetId);
    }

    foreach($rules as $rule) {
      $rulesetName = $rule->getRuleset()->getName();
      $rule->setRuleset($rulesets[$rulesetName]);
      $orm->merge($rule);
    }

    $orm->flush();

  }
  
  /**
   * Enregistre des regles
   * @param type $rulesetName
   * @return int
   * @author William Petit
   */
  protected function ensureRuleset($rulesetName) {
    $orm = $this->app['orm'];
    $ruleset = $orm->getRepository(Ruleset::class)->findOneByName($rulesetName);
    if($ruleset) return $ruleset->getId();
    $ruleset = new Ruleset();
    $ruleset->setName($rulesetName);
    $orm->persist($ruleset);
    $orm->flush();
    return $ruleset->getId();
  }

  /**
   * Nettoie un jeu de regle
   * @param string $cleanupRuleset
   * @return int
   */
  protected function cleanup($cleanupRuleset) {
    $orm = $this->app['orm'];
    $qb = $orm->getRepository(CustomRule::class)->createQueryBuilder('r');
    if( $cleanupRuleset !== self::ALL_RULESETS ) {
      $qb->join('r.ruleset', 'rs');
      $qb->where($qb->expr()->eq('rs.name', $qb->expr()->literal($cleanupRuleset)));
    }
    $rules = $qb->getQuery()->getResult();
    foreach($rules as $r) $orm->remove($r);
    $orm->flush();
    return count($rules);
  }
  
  /**
   * Test si un jeu de regle existe
   * @param string $rulesetName
   * @return boolean
   */
  protected function rulesetExists($rulesetName) {
    $orm = $this->app['orm'];
    return $orm->getRepository(Ruleset::class)->exists($rulesetName);
  }

}
