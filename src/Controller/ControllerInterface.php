<?php

namespace Karambol\Controller;

use Karambol\KarambolApp;

interface ControllerInterface
{
  public function bindTo(KarambolApp $app);
  public function mount(KarambolApp $app);
}
