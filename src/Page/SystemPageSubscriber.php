<?php

namespace Karambol\Page;

use Karambol\KarambolApp;
use Karambol\Page\Page;
use Karambol\VirtualSet\ItemCountEvent;
use Karambol\VirtualSet\ItemSearchEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SystemPageSubscriber implements EventSubscriberInterface {

  /**
   * @var string
   */
  protected $pageLabel;

  /**
   * @var string
   */
  protected $pageUrl;

  /**
   * @var string
   */
  protected $pageSlug;

  public function __construct($pageLabel, $pageUrl, $pageSlug) {
    $this->pageLabel = $pageLabel;
    $this->pageUrl = $pageUrl;
    $this->pageSlug = $pageSlug;
  }

  public static function getSubscribedEvents() {
    return [
      ItemSearchEvent::NAME => 'onSearchPages',
      ItemCountEvent::NAME => 'onCountPages'
    ];
  }

  public function onSearchPages(ItemSearchEvent $event) {
    $page = new Page($this->pageLabel, $this->pageUrl, $this->pageSlug);
    $event->addItem($page);
  }

  public function onCountPages(ItemCountEvent $event) {
    $event->add(1);
  }

}
