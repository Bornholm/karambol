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
 * Interface pour les ressources
 * @package Karambol
 * 
 * @license AGPLv3
 * @author William Petit
 */
interface ResourceInterface {
  
  /**
   * Renvoi le type de la ressource
   * @return string
   * @author William Petit
   */
  public function getResourceType();
  
  /**
   * Retourne l'identifiant de la ressource
   * @return int
   * @author William Petit
   */
  public function getResourceId();
  
  /**
   * Retourne les propriétés de la ressource
   * @return ResourceProperty
   * @author William Petit
   */
  public function getResourceProperty();
}
