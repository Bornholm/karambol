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
 * Exception email inconnu
 * @package Karambol
 * @license AGPLv3
 * @author William Petit
 */
class EmailUnknownException extends \Exception
{
  /**
   * Nom d'utlisateur
   * @var string
   * @author William Petit
   */
  protected $username;
  
  /**
   * Constructeur de classe
   * @param string $username Nom d'utilisateur
   * @author William Petit
   */
  public function __construct($username) {
    $this->username = $username;
    parent::__construct(sprintf('The account with the username "%s" does not have an email address !', $this->getUsername()), 0);
  }
  
  /**
   * Renvoi le nom d'utilisateur
   * @return string
   * @author William Petit
   */
  public function getUsername() {
    return $this->username;
  }

}
