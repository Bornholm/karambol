<?php

namespace Karambol\AccessControl;
use Karambol\AccessControl\ResourceOwnerInterface;

class ResourceSelector {

  const SELF_OWNER = 'self';

  protected $resourceType;
  protected $resourceRererences;

  public function __construct($resourceType, array $resourceRererences = []) {
    $this->resourceType = $resourceType;
    $this->resourceRererences = $resourceRererences;
  }

  public function getResourceType() {
    return $this->resourceType;
  }

  public function getResourceReferences() {
    return $this->resourceRererences;
  }

  public function matches(ResourceInterface $resource) {

    $resourceTypeMatches = $this->matchResourceType($resource->getResourceType());
    if(!$resourceTypeMatches) return false;

    return $this->matchResourceReferences($resource->getResourceId());

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

}
