<?php

namespace Karambol\AccessControl;

use Karambol\AccessControl\ResourceInterface;
use Karambol\RuleEngine\Context\ProtectableInterface;

class Resource implements ResourceInterface, ProtectableInterface {

  /* @var array */
  protected $resourceType;
  protected $resourceId;

  public function __construct($resourceType, $resourceId = null) {
    $this->resourceType = is_array($resourceType) ? $resourceType: [$resourceType];
    $this->resourceId = $resourceId;
  }

  public function getResourceId() {
    return $this->resourceId;
  }

  public function getResourceType() {
    return $this->resourceType;
  }

  public function getExposedAttributes() {
    $type = $this->getResourceType();
    return [
      'id' => $this->getResourceId(),
      'type' => $type[0],
      'property' => count($type) > 1 ? $type[1] : null
    ];
  }

  public function __toString() {
    return sprintf(
      '%s[%s]',
      implode('.', $this->getResourceType()),
      $this->getResourceId()
    );
  }

}
