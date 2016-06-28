<?php

namespace Karambol\Setting;

use Karambol\VirtualSet\VirtualSet;
use Karambol\KarambolApp;
use Symfony\Component\Yaml\Yaml;

class SettingService extends VirtualSet {

  protected $configFilePath;
  protected $values = [];

  public function __construct($configFilePath, $values = []) {
    $this->configFilePath = $configFilePath;
    $this->values = $values;
  }

  public function get($name) {
    $entry = $this->findOne(['name' => $name]);
    return $entry ? $entry->getValue() : null;
  }

  public function find(array $criteria = [], $limit = null) {
    $values = $this->values;
    $results = parent::find($criteria, $limit);
    foreach($results as $entry) {
      $hasValue = isset($values[$entry->getName()]);
      if($hasValue) $entry->setValue($values[$entry->getName()]);
    }
    return $results;
  }

  public function save($values) {
    $this->values = array_merge($this->values, $values);
    $yaml = Yaml::dump(['settings' => $this->values], 2, 2);
    file_put_contents($this->configFilePath, $yaml);
    return $this;
  }

}
