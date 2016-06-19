<?php

namespace Karambol\Page;

use Karambol\KarambolApp;
use Karambol\Page\Page;
use Karambol\VirtualSet\ItemCountEvent;
use Karambol\VirtualSet\ItemSearchEvent;
use Karambol\VirtualSet\ItemIterateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BasePagesSubscriber implements EventSubscriberInterface {

  protected $app;

  public function __construct(KarambolApp $app) {
    $this->app = $app;
  }

  public static function getSubscribedEvents() {
    return [
      ItemSearchEvent::NAME => 'onSearchPages',
      ItemIterateEvent::NAME => 'onIteratePages',
      ItemCountEvent::NAME => 'onCountPages'
    ];
  }

  public function onSearchPages(ItemSearchEvent $event) {

    $criteria = $event->getCriteria();

    $customPages = $this->getCustomPages($criteria);
    foreach($customPages as $page) {
      $event->addResult($page);
    }

    $systemPages = $this->getBaseSystemPages();
    foreach($systemPages as $page) {
      $event->addResult($page);
    }

  }

  public function onIteratePages(ItemIterateEvent $event) {

    $customPages = $this->getCustomPages();
    $event->addIterator(new \ArrayIterator($customPages));

    $systemPages = $this->getBaseSystemPages();
    $event->addIterator(new \ArrayIterator($systemPages));

  }

  public function onCountPages(ItemCountEvent $event) {

    $event->add($this->countCustomPages());

    $systemPages = $this->getBaseSystemPages();
    $event->add(count($systemPages));

  }

  protected function countCustomPages() {
    $orm = $this->app['orm'];
    return $orm->getRepository('Karambol\Entity\CustomPage')->count();
  }

  protected function getCustomPages($criteria = []) {
    $orm = $this->app['orm'];
    return $orm->getRepository('Karambol\Entity\CustomPage')->findAll($criteria);
  }

  protected function getBaseSystemPages() {

    $translator = $this->app['translator'];
    $urlGen = $this->app['url_generator'];

    return [
      new Page($translator->trans('pages.home'), $urlGen->generate('home'), 'home'),
      new Page($translator->trans('pages.admin'), $urlGen->generate('admin'), 'admin'),
      new Page($translator->trans('pages.login'), $urlGen->generate('login'), 'login'),
      new Page($translator->trans('pages.logout'), $urlGen->generate('logout'), 'logout')
    ];

  }

}
