<?php

namespace Karambol\RuleEngine;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Doctrine\ORM\EntityManager;

class RuleEngineService extends EventDispatcher {

  protected $em;

  public function __construct(EntityManager $em) {
    $this->em = $em;
  }

  public function execute($rulesCategory, $context) {
    
  }

}
