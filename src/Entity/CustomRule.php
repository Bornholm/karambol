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
use Karambol\RuleEngine\RuleInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="rules")
 * @ORM\HasLifecycleCallbacks
 */
class CustomRule implements RuleInterface {

  const ORIGIN_SEED = 'seed';
  const ORIGIN_BACKOFFICE = 'backoffice';
  const ORIGIN_COMMAND = 'command';

  /**
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\Column(type="integer")
   */
  protected $id;

  /**
   * @ORM\Column(name="`condition`", type="text", nullable=false)
   */
  protected $condition;

  /**
   * @ORM\Column(type="text", nullable=false)
   */
  protected $action;

  /**
   * @ORM\Column(type="integer", name="weight")
   */
  protected $weight = 0;

  /**
   * @ORM\Column(type="text", length=64, nullable=false)
   */
  protected $origin = self::ORIGIN_BACKOFFICE;

  /**
   * @ORM\ManyToOne(targetEntity="Karambol\Entity\Ruleset", inversedBy="rules", cascade="all")
   * @ORM\JoinColumn(name="ruleset", referencedColumnName="id", onDelete="CASCADE", nullable=false)
   */
  protected $ruleset;

  public function getId() {
    return $this->id;
  }

  public function getCondition() {
    return $this->condition;
  }

  public function setCondition($condition) {
    $this->condition = $condition;
  }

  public function getAction() {
    return $this->action;
  }

  public function setAction($action) {
    $this->action = $action;
  }

  public function setActions(array $actions) {
    $this->action = join(PHP_EOL, $actions);
  }

  public function getActions() {
    return preg_split('/(\n)+|(\r\n)+/', $this->action);
  }

  public function getRuleset() {
    return $this->ruleset;
  }

  public function setRuleset($ruleset = null) {
    $this->ruleset = $ruleset;
  }

  /**
   * @return
   */
  public function getWeight()
  {
    return $this->weight;
  }

  /**
   * @param  $weight
   *
   * @return $this
   */
  public function setWeight($weight)
  {
    $this->weight = $weight;
    return $this;
  }

  /**
   * @return
   */
  public function getOrigin()
  {
    return $this->origin;
  }

  /**
   * @param  $origin
   *
   * @return static
   */
  public function setOrigin($origin)
  {
    $this->origin = $origin;
    return $this;
  }

}
