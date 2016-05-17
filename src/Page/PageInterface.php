<?php

namespace Karambol\Page;

use Karambol\KarambolApp;

interface PageInterface
{
  public function getUrl();
  public function getSlug();
  public function isEditable();
}
