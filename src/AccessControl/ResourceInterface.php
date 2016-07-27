<?php

namespace Karambol\AccessControl;

interface ResourceInterface {
  public function getResourceType();
  public function getResourceId();
  public function getResourceOwnerId();
}
