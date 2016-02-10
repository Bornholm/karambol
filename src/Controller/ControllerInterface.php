<?php

namespace Karambol\Controller;

use Karambol\KarambolApp;

interface ControllerInterface
{
  public function setApp(KarambolApp $app);
  public function mount();
}