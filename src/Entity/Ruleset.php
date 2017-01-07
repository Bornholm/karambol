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
namespace Karambol\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Karambol\Entity\Repository\RulesetRepository")
 * @ORM\Table(name="rulesets")
 */
class Ruleset {

  /**
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\Column(type="integer")
   */
  protected $id;

  /**
   * @ORM\Column(type="string", length=128, unique=true)
   */
  protected $name;

  /**
   * @ORM\OneToMany(targetEntity="Karambol\Entity\CustomRule", mappedBy="ruleset", orphanRemoval=true, cascade="all")
   * @ORM\OrderBy({"weight" = "DESC"})
   */
  protected $rules;


  public function __construct() {
    $this->rules = new ArrayCollection();
  }

  public function setId($id) {
    $this->id = $id;
    return $this;
  }

  public function getId() {
    return $this->id;
  }

  public function getRules() {
    return $this->rules;
  }

  public function setRules(ArrayCollection $rules) {
    $this->rules = $rules;
    return $this;
  }

  public function addRule(CustomRule $rule) {
    $rule->setRuleset($this);
    $this->rules->add($rule);
    return $this;
  }

  public function removeRule(CustomRule $rule) {
    $rule->setRuleset(null);
    $this->rules->removeElement($rule);
    return $this;
  }

  public function getName() {
    return $this->name;
  }

  public function setName($name) {
    $this->name = $name;
    return $this;
  }

}
