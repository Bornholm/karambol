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
namespace Karambol\AccessControl\Permission;

use Karambol\AccessControl\ResourceInterface;

/**
 * Collection de permission
 * @package Karambol
 * @license AGPLv3
 * @author William Petit
 */
class PermissionCollection implements \ArrayAccess, \Countable, \IteratorAggregate {
  
  /**
   * Tableau d'autorisation
   * @var array 
   */
  protected $authorizations = [];

  /**
   * Add an authorization to the rule engine context
   *
   * @throws \InvalidArgumentException
   *
   * @param string $action
   * @param string|ResourceInterface $resource
   * @return PersmissionCollection
   */
  public function add($action, $resource = null) {

    if($resource !== null &&
      (!is_string($resource) && !($resource instanceof ResourceInterface)) ) {
      throw new \InvalidArgumentException('The $resource parameter must be a valid resource selector or implements ResourceInterface !');
    }

    $this->authorizations[] = [
      'action' => $action,
      'selector' => is_string($resource) ? $resource : null,
      'resource' => $resource instanceof ResourceInterface ? $resource : null,
    ];

    return $this;

  }
  
  /**
   * Test l'existence d'une cle
   * @param int $offset
   * @return boolean
   */
  public function offsetExists($offset) {
    return array_key_exists($offset, $this->authorizations);
  }
  
  /**
   * Renvoi un enregistrement
   * @param int $offset
   * @return array
   */
  public function offsetGet($offset) {
    return $this->authorizations[$offset];
  }
  
  /**
   * Defini la valeur d'un enregistrement
   * @param int $offset
   * @param array $value
   * @return PersmissionCollection
   */
  public function offsetSet($offset, $value) {
    $this->authorizations[$offset] = $value;
    return $this;
  }
  
  /**
   * Supprime un enregistrement
   * @param int $offset 
   * @return PermissionCollection
   */
  public function offsetUnset($offset) {
    unset($this->authorizations[$offset]);
    return $this;
  }
  
  /**
   * Compte les enregistrements
   * @return int
   */
  public function count() {
    return count($this->authorizations);
  }
  
  /**
   * @todo description
   * @return \ArrayIterator
   */
  public function getIterator() {
    return new \ArrayIterator($this->authorizations);
  }

}
