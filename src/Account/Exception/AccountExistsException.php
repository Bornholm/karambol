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
namespace Karambol\Account\Exception;

/**
 * Exception compte existant
 * @package Karambol
 * @license AGPLv3
 * @author William Petit
 */
class AccountExistsException extends \Exception
{
  /**
   * Nom d'utilisateur
   * @var username
   */
  protected $username;
  
  /**
   * Constructeur de classe
   * @param string $username Nom d'utilisateur
   */
  public function __construct($username) {
    $this->username = $username;
    parent::__construct(sprintf('The account with the username "%s" already exists !', $this->getUsername()), 0);
  }
  
  /**
   * Retourne le nom d'utilisateur
   * @return string
   */
  public function getUsername() {
    return $this->username;
  }

}
