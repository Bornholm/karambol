<?php

namespace Karambol\AccessControl;

interface ResourceOwnerInterface {
  public function owns(ResourceInterface $resource);
}
