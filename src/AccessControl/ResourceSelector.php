<?php

namespace Karambol\AccessControl;
use Karambol\AccessControl\ResourceOwnerInterface;

class ResourceSelector {

  /* @var array */
  protected $resourceType;
  protected $resourceRererences;

  public function __construct($resourceType, array $resourceRererences = []) {
    $this->resourceType = is_array($resourceType) ? $resourceType: [$resourceType];
    $this->resourceRererences = $resourceRererences;
  }

  public function getResourceType() {
    return $this->resourceType;
  }

  public function getResourceReferences() {
    return $this->resourceRererences;
  }

  public function matches(ResourceInterface $resource) {

    $resourceType = $resource->getResourceType();

    $rootTypeMatches = $this->matchRootType($resourceType[0]);
    if(!$rootTypeMatches) return false;

    $propertyType = count($resourceType) > 1 ? $resourceType[1] : null;
    $propertyTypeMatches = $this->matchPropertyType($propertyType);
    if(!$propertyTypeMatches) return false;

    return $this->matchResourceReferences($resource->getResourceId());

  }

  protected function matchRootType($rootType) {
    return fnmatch($this->getResourceType()[0], $rootType);
  }

  protected function matchPropertyType($resourcePropertyType = null) {

    $selectorType = $this->getResourceType();
    $selectorPropertyType = count($selectorType) > 1 ? $selectorType[1] : null;

    $selectorHasProperty = !empty($selectorPropertyType);
    $resourceHasProperty = !empty($resourcePropertyType);

    if(!$resourceHasProperty && !$selectorHasProperty) return true;
    if($resourceHasProperty && !$selectorHasProperty) return true;
    if(!$resourceHasProperty && $selectorHasProperty) return false;

    return fnmatch($selectorPropertyType, $resourcePropertyType);

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
