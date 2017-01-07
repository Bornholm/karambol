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
 * Liste des actions
 * @package Karambol
 * 
 * @license AGPLv3
 * @author William Petit
 */
class BaseActions {

  // Special actions
  
  /**
   * Toutes les actions
   * @var string
   * @author William Petit
   */
  const ALL = '*';
 
  /**
   * Acces
   * @var string
   * @author William Petit
   */
  const ACCESS = 'access';
  
  /**
   * Role
   * @var string
   * @author William Petit
   */
  const ROLE = 'role';

  // CRUD operations
  
  /**
   * Create operation
   * @var string
   * @author William Petit
   */
  const CREATE = 'create';
  
  /**
   * Read operation
   * @var string
   * @author William Petit
   */
  const READ = 'read';
  
  /**
   * Update operation
   * @var string
   * @author William Petit
   */
  const UPDATE = 'update';
  
  /**
   * Delete operation
   * @var string
   * @author William Petit
   */
  const DELETE = 'delete';

}
