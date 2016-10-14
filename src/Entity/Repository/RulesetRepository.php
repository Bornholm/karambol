<?php

namespace Karambol\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class RulesetRepository extends EntityRepository {

  /**
   * Check if a ruleset with the given already exists in the database
   *
   * @param string $rulesetName The ruleset's name
   * @return boolean
   */
  public function exists($rulesetName) {
    $qb = $this->createQueryBuilder('r');
    $qb->select('count(r)')
      ->where($qb->expr()->eq('r.name', $qb->expr()->literal($rulesetName)))
    ;
    return $qb->getQuery()->getSingleScalarResult() == 1;
  }

}
