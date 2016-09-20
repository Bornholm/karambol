<?php

namespace Karambol\Page;

use Karambol\KarambolApp;
use Karambol\Page\Page;
use Karambol\VirtualSet\ItemCountEvent;
use Karambol\VirtualSet\ItemSearchEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BasePagesSubscriber implements EventSubscriberInterface {

  protected $app;

  public function __construct(KarambolApp $app) {
    $this->app = $app;
  }

  public static function getSubscribedEvents() {
    return [
      ItemSearchEvent::NAME => 'onSearchPages',
      ItemCountEvent::NAME => 'onCountPages'
    ];
  }

  public function onSearchPages(ItemSearchEvent $event) {

    $criteria = $event->getSelector();

    $customPages = $this->getCustomPages($criteria);
    $event->addItems($customPages);

    $systemPages = $this->getBaseSystemPages();
    $event->addItems($systemPages);

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
      new Page($translator->trans('pages.logout'), $urlGen->generate('logout'), 'logout'),
      new Page($translator->trans('pages.register'), $urlGen->generate('register'), 'register'),
      new Page($translator->trans('pages.profile'), $urlGen->generate('profile'), 'profile')
    ];

  }

}
