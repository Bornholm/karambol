<?php

namespace Karambol\AccessControl;

use Karambol\AccessControl\ResourceInterface;
use Karambol\RuleEngine\Context\ProtectableInterface;

class Resource implements ResourceInterface, ProtectableInterface {

  protected $resourceType;
  protected $resourceId;
  protected $resourceProperty;

  public function __construct($resourceType, $resourceId, $propertyName = null) {
    $this->resourceType = $resourceType;
    $this->resourceId = $resourceId;
    if($propertyName !== null) $this->resourceProperty = new ResourceProperty($this, $propertyName);
  }

  public function getResourceId() {
    return $this->resourceId;
  }

  public function getResourceType() {
    return $this->resourceType;
  }

  public function getResourceProperty() {
    return $this->resourceProperty;
  }

  public function getExposedAttributes() {
    return [
      'id' => $this->getResourceId(),
      'type' => $this->getResourceType(),
      'property' => $this->getResourceProperty() ? $this->getResourceProperty()->getName() : null
    ];
  }

  public function __toString() {
    return sprintf(
      '%s%s[%s]',
      $this->getResourceType(),
      $this->getResourceProperty() ? '.'.$this->getResourceProperty()->getName() : '',
      $this->getResourceId()
    );
  }

}
