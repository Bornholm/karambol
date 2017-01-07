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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Karambol\KarambolApp;
use Karambol\Entity\User;
use Karambol\Entity\Ruleset;
use Karambol\Entity\CustomRule;
use Karambol\RuleEngine\RuleEngine;

/**
 * Commande promotion de compte
 * @package Karambol
 * @since 1.0.0
 * @license AGPLv3
 * @author William Petit
 */
class PromoteAccountCommand extends Command
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
   * @author Benjamin Gaudé
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
      ->setName('karambol:account:promote')
      ->setDescription('Promote a registered account to admin.')
      ->addOption('universal', 'u', InputOption::VALUE_NONE, 'Use a universal "promotion" (without the user of "ROLE_ADMIN")')
      ->addArgument(
        'username',
        InputArgument::REQUIRED,
        'The account\'s username'
      )
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

    $username = $input->getArgument('username');
    $universal = $input->getOption('universal');
    $orm = $this->app['orm'];

    $user = $orm->getRepository(User::class)->findOneByUsername($username);

    if(!$user) {
      $output->writeln(sprintf('<error>The account with username "%s" does not exists.</error>', $username));
      exit(1);
    }

    $rule = new CustomRule();
    $rule->setCondition(sprintf('user.id == %s', $user->getId()));

    if($universal) {
      $output->writeln(sprintf('<comment>Using universal rule...</comment>', $username));
      $rule->setAction('allow("*", "*")');
    } else {
      $output->writeln(sprintf('<comment>Using role based rule...</comment>', $username));
      $rule->setAction('addRole("ROLE_ADMIN")');
    }

    $rule->setWeight(100);
    $rule->setOrigin(CustomRule::ORIGIN_COMMAND);

    $ruleset = $orm->getRepository('Karambol\Entity\Ruleset')->findOneByName(RuleEngine::ACCESS_CONTROL);
    if(!$ruleset) {
      $ruleset = new Ruleset();
      $ruleset->setName(RuleEngine::ACCESS_CONTROL);
      $orm->persist($ruleset);
      $orm->flush();
    }
    $rule->setRuleset($ruleset);

    $orm->persist($rule);
    $orm->flush();

    $output->writeln('<info>Account promoted.</info>');

  }

}
