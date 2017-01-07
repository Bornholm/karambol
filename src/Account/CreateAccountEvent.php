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
namespace Karambol\Account;

use Symfony\Component\EventDispatcher\Event;
use Karambol\Entity\User;

/**
 * Evenement creation de compte
 * @package Karambol
 * @license AGPLv3
 * @author William Petit
 */
class CreateAccountEvent extends Event {
  
  /**
   * Nom event avant creation
   * @var string
   * @author William Petit
   */
  const BEFORE_CREATE_ACCOUNT = 'account.before_create';
  
  /**
   * Nom event apres creation
   * @var string
   * @author William Petit
   */
  const AFTER_CREATE_ACCOUNT = 'account.after_create';
  
  /**
   * Utilisateur
   * @var User
   * @author William Petit
   */
  protected $user;
  
  /**
   * Mot de passe
   * @var string
   * @author William Petit
   */
  protected $clearTextPassword;
  
  /**
   * Constructeur de classe
   * @param User $user
   * @param string $clearTextPassword
   * @author William Petit
   */
  public function __construct(User $user, $clearTextPassword) {
    $this->user = $user;
    $this->clearTextPassword = $clearTextPassword;
  }
  
  /**
   * Renvoi l'utilisateur
   * @return User
   * @author William Petit
   */
  public function getUser() {
    return $this->user;
  }
  
  /**
   * Renvoi le mot de passe
   * @return string
   * @author William Petit
   */
  public function getClearTextPassword() {
    return $clearTextPassword;
  }

}
