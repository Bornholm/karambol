<?php

namespace Karambol\AccessControl;

use Karambol\AccessControl\ResourceOwnerInterface;

class ResourceOwner implements ResourceOwnerInterface {

  protected $ownerId;

  public function __construct($ownerId) {
    $this->ownerId = $ownerId;
  }

  public function getOwnerId() {
    return $this->ownerId;
  }

}
