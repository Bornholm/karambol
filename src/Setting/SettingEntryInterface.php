<?php

namespace Karambol\Setting;

use Karambol\VirtualSet\VirtualSet;

interface SettingEntryInterface {

  public function getName();
  public function getValue();
  public function setValue($value);
  public function getDefaultValue();
  public function getDescription();
  public function getConstraints();
  public function getChoices();

}
