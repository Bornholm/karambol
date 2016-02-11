<?php

namespace Karambol\Controller;

use Karambol\KarambolApp;

interface ControllerInterface
{
  public function mount(KarambolApp $app);
}
