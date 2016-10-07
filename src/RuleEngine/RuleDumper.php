<?php

namespace Karambol\RuleEngine;

use Doctrine\ORM\EntityManager;
use Karambol\Entity\RuleSet;
use Karambol\Entity\CustomRule;
use Symfony\Component\Yaml\Yaml;

class RuleDumper {

  const VERSION = 0;

  /**
   * @var rine\ORM\EntityManager
   **/
  protected $orm;

  public function __construct(EntityManager $orm) {
    $this->orm = $orm;
  }

  /**
   * Dump rules based on the provided filters as string
   *
   * @var string $rulesetFilter The ruleset filter
   * @var string $originFilter The origin filter
   * @return string
   */
  public function dump($rulesetFilter = null, $originFilter = null) {

    $orm = $this->orm;

    $dump = [
      'version' => self::VERSION,
      'rules' => []
    ];

    $rules = $orm->getRepository(CustomRule::class)->findAll();
    foreach($rules as $rule) {

      $ruleset = $rule->getRuleset()->getName();
      $origin = $rule->getOrigin();

      $sameRuleset = $rulesetFilter === null || $ruleset === $rulesetFilter;
      $sameOrigin = $originFilter === null || $origin === $originFilter;

      if($sameRuleset && $sameOrigin) {
        $dump['rules'][] = [
          'origin' => $origin,
          'set' => $ruleset,
          'order' => $rule->getOrder(),
          'condition' => $rule->getCondition(),
          'actions' => $rule->getActions()
        ];
      }

    }

    $now = new \DateTime('now');
    $yaml = sprintf('# Karambol rules dump -- %s'.PHP_EOL, $now->format('Y-m-d H:i:s'));
    $yaml .= Yaml::dump($dump, 3, 2);

    return $yaml;

  }

  /**
   * Load rules from a previous dump
   *
   * @throws Symfony\Component\Yaml\Exception\ParseException
   *
   * @var string $dumpStr The dump content
   * @var boolean $cleanup Should we append the set to the existing rules or cleanup before ?
   * @return $this
   */
  public function load($dumpStr, $cleanup = false) {

    $dump = Yaml::parse($dumpStr);

    

  }

}
