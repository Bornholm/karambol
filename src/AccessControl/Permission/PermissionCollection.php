<?php

namespace Karambol\AccessControl\Permission;

use Karambol\AccessControl\ResourceInterface;

class PermissionCollection implements \ArrayAccess, \Countable, \IteratorAggregate {

  protected $authorizations = [];

  /**
   * Add an authorization to the rule engine context
   *
   * @throws \InvalidArgumentException
   *
   * @param string $action
   * @param string|ResourceInterface $resource
   * @return $this
   */
  public function add($action, $resource = null) {

    if($resource !== null &&
      (!is_string($resource) && !($resource instanceof ResourceInterface)) ) {
      throw new \InvalidArgumentException('The $resource parameter must be a valid resource selector or implements ResourceInterface !');
    }

    $this->authorizations[] = [
      'action' => $action,
      'selector' => is_string($resource) ? $resource : null,
      'resource' => $resource instanceof ResourceInterface ? $resource : null,
    ];

    return $this;

  }

  public function offsetExists($offset) {
    return array_key_exists($offset, $this->authorizations);
  }

  public function offsetGet($offset) {
    return $this->authorizations[$offset];
  }

  public function offsetSet($offset, $value) {
    $this->authorizations[$offset] = $value;
    return $this;
  }

  public function offsetUnset($offset) {
    unset($this->authorizations[$offset]);
    return $this;
  }

  public function count() {
    return count($this->authorizations);
  }

  public function getIterator() {
    return new \ArrayIterator($this->authorizations);
  }

}
