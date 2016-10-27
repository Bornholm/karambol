<?php

namespace Karambol\AccessControl\Permission;

use Karambol\AccessControl\Parser\ResourceSelectorParser;

class PermissionChecker {

  protected $rejections;
  protected $authorizations;

  public function __construct(PermissionCollection $authorizations, PermissionCollection $rejections = null) {
    $this->rejections = $rejections;
    $this->authorizations = $authorizations;
  }

  public function isAllowed($action, $resource) {

    $rejected = $this->rejections !== null ?
      $this->checkAuthorizations($action, $resource, $this->rejections) :
      false
    ;

    if($rejected) return false;

    $authorized = $this->checkAuthorizations($action, $resource, $this->authorizations);
    return $authorized;

  }

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
