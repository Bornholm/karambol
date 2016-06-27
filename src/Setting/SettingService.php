<?php

namespace Karambol\Setting;

use Karambol\VirtualSet\VirtualSet;
use Karambol\KarambolApp;
use Karambol\Entity\Setting;
use Karambol\Util\AppAwareTrait;

class SettingService extends VirtualSet {

  use AppAwareTrait;

  public function get($entryName) {
    $entry = $this->findOne(['name' => $entryName]);
    return $entry ? $entry->getValue() : null;
  }

  public function find(array $criteria = [], $limit = null) {

    $results = parent::find($criteria, $limit);

    $orm = $this->app['orm'];
    $repo = $orm->getRepository('Karambol\Entity\Setting');

    foreach($results as $entry) {
      $entryName = $entry->getName();
      $savedSetting = $repo->findOneByName($entryName);
      if(!$savedSetting) continue;
      $entry->setValue($savedSetting->getValue());
    }

    return $results;

  }

  public function save($entryName, $entryValue) {

    $orm = $this->app['orm'];

    $setting = $orm->getRepository('Karambol\Entity\Setting')->findOneByName($entryName);

    if(!$setting) {
      $setting = new Setting();
      $setting->setName($entryName);
      $orm->persist($setting);
    }

    $setting->setValue($entryValue);
    $orm->flush();

    return $this;

  }

}
