<?php

namespace Karambol\Setting;

use Karambol\VirtualSet\VirtualSet;

class SettingService extends VirtualSet {

  public function get($entryName) {
    $entry = $this->findOne(['name' => $entryName]);
    return $entry ? $entry->getValue() : null;
  }

}
