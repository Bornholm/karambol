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

use Karambol\AccessControl\Parser\ResourceSelectorParser;

/**
 * Check permission
 * @package Karambol
 * @since 1.0.0
 * @license AGPLv3
 * @author William Petit
 */
class PermissionChecker {
  
  /**
   * Les interdictions
   * @var collection 
   */
  protected $rejections;
  
  /**
   * Les authorisations
   * @var collection 
   */
  protected $authorizations;

   /**
    * Constructeur de classe
    * @param \Karambol\AccessControl\Permission\PermissionCollection $authorizations Collection des authorisations
    * @param \Karambol\AccessControl\Permission\PermissionCollection $rejections Collection des interdictions
    */
  public function __construct(PermissionCollection $authorizations, PermissionCollection $rejections = null) {
    $this->rejections = $rejections;
    $this->authorizations = $authorizations;
  }
  
  /**
   * Check si l'action est autorisee sur la ressource
   * @param string $action Action
   * @param object $resource Ressource
   * @return boolean
   */
  public function isAllowed($action, $resource) {

    $rejected = $this->rejections !== null ?
      $this->checkAuthorizations($action, $resource, $this->rejections) :
      false
    ;

    if($rejected) return false;

    $authorized = $this->checkAuthorizations($action, $resource, $this->authorizations);
    return $authorized;

  }
  
  /**
   * Verifie les autorisations d'une resource pour une action
   * @param string $action Chaine
   * @param object $resource Ressource
   * @param \Karambol\AccessControl\Permission\PermissionCollection $authorizations
   * @return boolean
   */
  protected function checkAuthorizations($action, $resource, PermissionCollection $authorizations) {

    $parser = new ResourceSelectorParser();

    foreach($authorizations as $auth) {

      $actionsMatches = $auth['action'] === '*' || $auth['action'] === $action;
      if(!$actionsMatches) continue;

      if($auth['resource'] !== null && $auth['resource'] === $resource) {
        return true;
      }

      if($auth['selector'] !== null) {

        $selector = $parser->parse($auth['selector']);
        $resourceMatchesSelector = $selector->matches($resource);

        if($resourceMatchesSelector) {
          return true;
        }

      }

    }

    return false;

  }

}
