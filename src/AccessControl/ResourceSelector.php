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

/**
 * Selection de ressource
 * @package Karambol
 * 
 * @license AGPLv3
 * @author William Petit
 */
class ResourceSelector {
  
  /**
   * Type de la ressource
   * @var string
   * @author William Petit
   */
  protected $resourceType;
  
  /**
   * Références de la ressource
   * @var array
   * @author William Petit
   */
  protected $resourceRererences;
  
  /**
   * Nom de la propriete
   * @var string
   * @author William Petit
   */
  protected $resourcePropertyName;
  
  /**
   * Constructeur de classe
   * @param string $resourceType Type de la ressource
   * @param array $resourceRererences References de la ressource
   * @param string $resourcePropertyName Nom de la propriete
   * @author William Petit
   */
  public function __construct($resourceType, array $resourceRererences = [], $resourcePropertyName = null) {
    $this->resourceType = $resourceType;
    $this->resourceRererences = $resourceRererences;
    $this->resourcePropertyName = $resourcePropertyName;
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
   * Retourne les references de la ressource
   * @return array
   * @author William Petit
   */
  public function getResourceReferences() {
    return $this->resourceRererences;
  }
  
  /**
   * Retourne la nom de la propriete
   * @return string
   * @author William Petit
   */
  public function getResourcePropertyName() {
    return $this->resourcePropertyName;
  }
  
  /**
   * Match une autre ressource
   * @param \Karambol\AccessControl\ResourceInterface $resource Ressource
   * @return boolean
   * @author William Petit
   */
  public function matches(ResourceInterface $resource) {

    $resourceTypeMatches = $this->matchResourceType($resource->getResourceType());
    if(!$resourceTypeMatches) return false;

    $resourcePropertyMatches = $this->matchResourceProperty($resource->getResourceProperty());
    if(!$resourcePropertyMatches) return false;

    return $this->matchResourceReferences($resource->getResourceId());

  }
  
  /**
   * Test si le type de ressource correspond
   * @param type $resourceType Type de la ressource
   * @return boolean
   * @author William Petit
   */
  protected function matchResourceType($resourceType) {
    return fnmatch($this->getResourceType(), $resourceType);
  }
  
  /**
   * Test si la propriete correspond
   * @param \Karambol\AccessControl\ResourceProperty $resourceProperty Propriete de la ressource
   * @return boolean
   * @author William Petit
   */
  protected function matchResourceProperty(ResourceProperty $resourceProperty = null) {
    $propertyName = $this->getResourcePropertyName();
    return $resourceProperty === null || fnmatch($propertyName, $resourceProperty->getName());
  }
  
  /**
   * Test si les references correspondent
   * @param type $resourceId identifiant de la ressource
   * @return boolean
   * @author William Petit
   */
  protected function matchResourceReferences($resourceId) {
    $references = $this->getResourceReferences();
    if(count($references) === 0) return true;
    foreach($references as $ref) {
      if(fnmatch($ref, $resourceId)) return true;
    }
    return false;
  }


}
