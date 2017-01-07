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
namespace Karambol\AccessControl;

use Karambol\AccessControl\ResourceInterface;
use Karambol\RuleEngine\Context\ProtectableInterface;

/**
 * Gestion des ressources
 * @package Karambol
 * 
 * @author William Petit
 */
class Resource implements ResourceInterface, ProtectableInterface {

  /**
   * Type de la ressource
   * @var string
   * @author William Petit
   */
  protected $resourceType;
  
  /**
   * Identifiant de la ressource
   * @var int
   * @author William Petit
   */
  protected $resourceId;
  
  /**
   * Proriete de la ressource
   * @var RessourceProperty
   */
  protected $resourceProperty;

  /**
   * Constructeur de class
   * @param string $resourceType Type de la ressource
   * @param int $resourceId Identifiant de la ressource
   * @param string $propertyName Nom de la ressource
   * @author William Petit
   */
  public function __construct($resourceType, $resourceId, $propertyName = null) {
    $this->resourceType = $resourceType;
    $this->resourceId = $resourceId;
    if($propertyName !== null) $this->resourceProperty = new ResourceProperty($this, $propertyName);
  }

  /**
   * Retourne l'identifiant de la ressource
   * @return int
   * @author William Petit
   */
  public function getResourceId() {
    return $this->resourceId;
  }

  /**
   * Retourne le type de la ressource
   * @return string
   * @author William Petit
   */
  public function getResourceType() {
    return $this->resourceType;
  }
  
  /**
   * Retourne les proprietes de la ressource
   * @return RessourceProperty
   * @author William Petit
   */
  public function getResourceProperty() {
    return $this->resourceProperty;
  }
  
  /**
   * Retourne les propriétés exposee de la ressource
   * @return array
   * @author William Petit
   */
  public function getExposedAttributes() {
    return [
      'id' => $this->getResourceId(),
      'type' => $this->getResourceType(),
      'property' => $this->getResourceProperty() ? $this->getResourceProperty()->getName() : null
    ];
  }
  
  /**
   * SURCHARGE de la methode
   * @return string
   * @author William Petit
   */
  public function __toString() {
    return sprintf(
      '%s%s[%s]',
      $this->getResourceType(),
      $this->getResourceProperty() ? '.'.$this->getResourceProperty()->getName() : '',
      $this->getResourceId()
    );
  }

}
