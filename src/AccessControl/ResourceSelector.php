<?php

namespace Karambol\AccessControl;
use Karambol\AccessControl\ResourceOwnerInterface;

class ResourceSelector {

  const SELF_OWNER = 'self';

  protected $resourceType;
  protected $resourceRererences;
  protected $ownerReferences;

  public function __construct($resourceType, array $resourceRererences = [], array $ownerReferences = []) {
    $this->resourceType = $resourceType;
    $this->resourceRererences = $resourceRererences;
    $this->ownerReferences = $ownerReferences;
  }

  public function getResourceType() {
    return $this->resourceType;
  }

  public function getResourceReferences() {
    return $this->resourceRererences;
  }

  public function getOwnerReferences() {
    return $this->ownerReferences;
  }

  public function match(ResourceInterface $resource, ResourceOwnerInterface $owner = null) {

    $resourceTypeMatches = $this->matchResourceType($resource->getResourceType());
    if(!$resourceTypeMatches) return false;

    $resourceIdMatches = $this->matchResourceReferences($resource->getResourceId());
    $resourceOwnerMatches = $this->matchOwnerReferences($resource->getResourceOwnerId(), $owner !== null ? $owner->getOwnerId() : null);

    return $resourceIdMatches && $resourceOwnerMatches;

  }

  protected function matchResourceType($resourceType) {
    return fnmatch($this->getResourceType(), $resourceType);
  }

  protected function matchResourceReferences($resourceId) {
    $references = $this->getResourceReferences();
    if(count($references) === 0) return true;
    foreach($references as $ref) {
      if(fnmatch($ref, $resourceId)) return true;
    }
    return false;
  }

  protected function matchOwnerReferences($resourceOwnerId, $ownerId) {

    if($resourceOwnerId === null) return true;

    $references = $this->getOwnerReferences();
    if(count($references) === 0) return true;

    foreach($references as $ref) {
      if(fnmatch($ref, $ownerId)) return true;
      if($ref === self::SELF_OWNER && $resourceOwnerId === $ownerId) return true;
    }

    return false;

  }

}
