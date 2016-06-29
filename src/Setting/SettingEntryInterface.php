<?php

namespace Karambol\Setting;

use Karambol\VirtualSet\VirtualSet;

interface SettingEntryInterface {

  public function getName();
  public function getLabel();
  public function getValue();
  public function setValue($value);
  public function getDefaultValue();
  public function getHelp();
  public function getConstraints();
  public function getChoices();

}
