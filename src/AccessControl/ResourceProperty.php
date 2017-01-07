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

/**
 * Propriete d'une ressource
 * @package Karambol
 * 
 * @license AGPLv3
 * @author William Petit
 */
class ResourceProperty {
  
  /**
   * Nom de la ressource
   * @var string
   * @author William Petit
   */
  protected $name;
  
  /**
   * Ressource parent
   * @var object
   * @author William Petit
   */
  protected $parent;

  /**
   * Constructeur de classe
   * @param ResourceInterface $parent Parent de la ressource
   * @param string $name Nom de la ressource
   * @author William Petit
   */
  public function __construct(ResourceInterface $parent, $name) {
    $this->parent = $parent;
    $this->name = $name;
  }
  
  /**
   * Retourne le nom de la ressource
   * @return string
   * @author William Petit
   */
  public function getName() {
    return $this->name;
  }
  
  /**
   * Retourne le parent d'une ressource
   * @return ResourceInterface
   * @author William Petit
   */
  public function getParent() {
    return $this->parent;
  }

}
