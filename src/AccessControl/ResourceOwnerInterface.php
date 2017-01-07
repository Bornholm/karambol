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
 * Interface pour la propriete d'une ressource
 * @package Karambol
 * @since 1.0.0
 * @license AGPLv3
 * @author William Petit
 */
interface ResourceOwnerInterface {
  
  /**
   * Check la propriete d'une ressource
   * @param \Karambol\AccessControl\ResourceInterface $resource
   * @author William Petit
   */
  public function owns(ResourceInterface $resource);
}
