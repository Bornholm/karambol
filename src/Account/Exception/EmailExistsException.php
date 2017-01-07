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
 * Exception email existe
 * @package Karambol
 * @since 1.0.0
 * @license AGPLv3
 * @author William Petit
 */
class EmailExistsException extends \Exception
{
  /**
   * Adresse email
   * @var string
   */
  protected $email;
  
  /**
   * Constructeur de classe
   * @param string $email
   */
  public function __construct($email) {
    $this->email = $email;
    parent::__construct(sprintf('The email "%s" is already associated to an account !', $this->getEmail()), 0);
  }
  
  /**
   * Retourne l'adresse email
   * @return string
   */
  public function getEmail() {
    return $this->email;
  }

}
