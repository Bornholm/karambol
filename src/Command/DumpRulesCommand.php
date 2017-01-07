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

use Karambol\Entity\CustomRule;
use Karambol\RuleEngine\Backup\Serializer;
use Karambol\RuleEngine\Backup\Transform\CustomRuleTransformer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Karambol\KarambolApp;
use Karambol\Entity\Ruleset;

/**
 * Commande sauvegarde regle
 * @package Karambol
 * @since 1.0.0
 * @license AGPLv3
 * @author William Petit
 */
class DumpRulesCommand extends Command
{
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
  protected function configure()
  {
    $this
      ->setName('karambol:rules:dump')
      ->setDescription('Export the rules from Karambol')
      ->addOption('ruleset', 'r', InputOption::VALUE_OPTIONAL, 'Only export the rules associated with this ruleset', null)
      ->addOption('origin', 'o', InputOption::VALUE_OPTIONAL, 'Only export the rules associated with this origin', null)
    ;
  }
  
  /**
   * Execute la commande
   * @param InputInterface $input
   * @param OutputInterface $output
   * @return int
   * @author William Petit
   */
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
  
  /**
   * Test si la regle existe
   * @param string $rulesetName
   * @return boolean
   * @author William Petit
   */
  protected function rulesetExists($rulesetName) {
    $orm = $this->app['orm'];
    return $orm->getRepository(Ruleset::class)->exists($rulesetName);
  }
  
  /**
   * Recupere des regles
   * @param String $rulesetFilter
   * @param string $originFilter
   * @return object
   * @author William Petit
   */
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
