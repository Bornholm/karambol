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
namespace Karambol\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Repository pour jeu de regles
 * @package Karambol
 * @license AGPLv3
 * @author William Petit
 */
class RulesetRepository extends EntityRepository {

  /**
   * Check if a ruleset with the given already exists in the database
   *
   * @param string $rulesetName The ruleset's name
   * @return boolean
   * @author William Petit
   */
  public function exists($rulesetName) {
    $qb = $this->createQueryBuilder('r');
    $qb->select('count(r)')
      ->where($qb->expr()->eq('r.name', $qb->expr()->literal($rulesetName)))
    ;
    return $qb->getQuery()->getSingleScalarResult() == 1;
  }

}
