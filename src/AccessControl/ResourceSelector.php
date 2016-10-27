<?php

namespace Karambol\AccessControl;
use Karambol\AccessControl\ResourceOwnerInterface;

class ResourceSelector {

  protected $resourceType;
  protected $resourceRererences;
  protected $resourcePropertyName;

  public function __construct($resourceType, array $resourceRererences = [], $resourcePropertyName = null) {
    $this->resourceType = $resourceType;
    $this->resourceRererences = $resourceRererences;
    $this->resourcePropertyName = $resourcePropertyName;
  }

  public function getResourceType() {
    return $this->resourceType;
  }

  public function getResourceReferences() {
    return $this->resourceRererences;
  }

  public function getResourcePropertyName() {
    return $this->resourcePropertyName;
  }

  public function matches(ResourceInterface $resource) {

    $resourceTypeMatches = $this->matchResourceType($resource->getResourceType());
    if(!$resourceTypeMatches) return false;

    $resourcePropertyMatches = $this->matchResourceProperty($resource->getResourceProperty());
    if(!$resourcePropertyMatches) return false;

    return $this->matchResourceReferences($resource->getResourceId());

  }

  protected function matchResourceType($resourceType) {
    return fnmatch($this->getResourceType(), $resourceType);
  }

  protected function matchResourceProperty(ResourceProperty $resourceProperty = null) {
    $propertyName = $this->getResourcePropertyName();
    return $resourceProperty === null || fnmatch($propertyName, $resourceProperty->getName());
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
