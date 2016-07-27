<?php

namespace Karambol\AccessControl;

use Karambol\AccessControl\ResourceInterface;

class Resource implements ResourceInterface {

  protected $resourceType;
  protected $resourceId;
  protected $ownerId;

  public function __construct($resourceType, $resourceId, $ownerId = null) {
    $this->resourceType = $resourceType;
    $this->resourceId = $resourceId;
    $this->ownerId = $ownerId;
  }

  public function getResourceId() {
    return $this->resourceId;
  }

  public function getResourceType() {
    return $this->resourceType;
  }

  public function getResourceOwnerId() {
    return $this->ownerId;
  }

}
