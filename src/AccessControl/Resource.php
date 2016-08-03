<?php

namespace Karambol\AccessControl;

use Karambol\AccessControl\ResourceInterface;

class Resource implements ResourceInterface {

  protected $resourceType;
  protected $resourceId;

  public function __construct($resourceType, $resourceId) {
    $this->resourceType = $resourceType;
    $this->resourceId = $resourceId;
  }

  public function getResourceId() {
    return $this->resourceId;
  }

  public function getResourceType() {
    return $this->resourceType;
  }

  public function __toString() {
    return sprintf('%s[%s]', $this->getResourceType(), $this->getResourceId());
  }

}
