<?php

namespace Karambol\Setting;

use Karambol\VirtualSet\VirtualSet;
use Karambol\Setting\SettingEntryInterface;

class SettingEntry implements SettingEntryInterface {

  protected $name;
  protected $label;
  protected $help;
  protected $defaultValue;
  protected $value;
  protected $constraints;
  protected $choices;

  public function __construct($name, $defaultValue) {
    $this->name = $name;
    $this->defaultValue = $defaultValue;
    $this->constraints = [];
    $this->choices = null;
  }

  public function getName() {
    return $this->name;
  }

  public function setLabel($label) {
    $this->label = $label;
    return $this;
  }

  public function getLabel() {
    return empty($this->label) ? $this->name : $this->label;
  }

  public function getDefaultValue() {
    return $this->defaultValue;
  }

  public function setDefaultValue($defaultValue) {
    $this->defaultValue = $defaultValue;
    return $this;
  }

  public function getValue() {
    return $this->value !== null ? $this->value : $this->defaultValue;
  }

  public function setValue($value) {
    $this->value = $value;
    return $this;
  }

  public function setHelp($help) {
    $this->help = $help;
    return $this;
  }

  public function getHelp() {
    return $this->help;
  }

  public function setConstraints($constraints) {
    $this->constraints = $constraints;
    return $this;
  }

  public function getConstraints() {
    return $this->constraints;
  }

  public function setChoices($choices) {
    $this->choices = $choices;
    return $this;
  }

  public function getChoices() {
    return $this->choices;
  }

}
