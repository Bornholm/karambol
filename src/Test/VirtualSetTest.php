<?php

namespace Karambol\Test;

use Karambol\VirtualSet\VirtualSet;
use Karambol\VirtualSet\ItemCountEvent;
use Karambol\VirtualSet\ItemSearchEvent;
use Karambol\VirtualSet\ItemIterateEvent;

class VirtualSetTest extends \PHPUnit_Framework_TestCase
{

  public function testVirtualSetBasicFind()
  {
    $set = new VirtualSet();

    $set->addListener(ItemSearchEvent::NAME, function(ItemSearchEvent $event) {
      $event->addResult(['key' => 'foo', 'val' => 1]);
      $event->addResult(['key' => 'bar', 'val' => 1]);
    });

    $results = $set->findAll();
    $this->assertEquals(2, count($results));

    $results = $set->findAll(['key' => 'bar', 'val' => 1]);
    $this->assertEquals(1, count($results));

    $result = $set->findOne();
    $this->assertEquals('foo', $result['key']);

    $result = $set->findOne(['key' => 'bar']);
    $this->assertNotNull($result);
    $this->assertEquals('bar', $result['key']);

  }

  public function testVirtualSetIteration()
  {
    $set = new VirtualSet();

    $set->addListener(ItemIterateEvent::NAME, function(ItemIterateEvent $event) {
      $event->addIterator(new \ArrayIterator([
        ['key' => 'foo'],
        ['key' => 'bar'],
        ['key' => 'baz']
      ]));
    });

    $set->addListener(ItemIterateEvent::NAME, function(ItemIterateEvent $event) {
      $event->addIterator(new \ArrayIterator([
        ['key' => 'doh']
      ]));
    });

    $this->assertInstanceOf('IteratorAggregate', $set);

    $totalIteratedItems = 0;
    foreach($set as $i => $item) {
      if($i === 0) $this->assertEquals('foo', $item['key']);
      if($i === 1) $this->assertEquals('bar', $item['key']);
      if($i === 2) $this->assertEquals('baz', $item['key']);
      if($i === 3) $this->assertEquals('doh', $item['key']);
      $totalIteratedItems++;
    }

    $this->assertEquals(4, $totalIteratedItems);

  }

  public function testVirtualSetNoListeners()
  {
    $set = new VirtualSet();

    $this->assertInstanceOf('IteratorAggregate', $set);

    $totalIteratedItems = 0;
    foreach($set as $i => $item) {
      $totalIteratedItems++;
    }

    $results = $set->findAll();

    $this->assertEquals(0, count($results));
    $this->assertEquals(0, count($set));
    $this->assertEquals(0, $totalIteratedItems);

  }

  public function testVirtualSetCount()
  {
    $set = new VirtualSet();
    $set->addListener(ItemCountEvent::NAME, function($event) {
      $event->add(2);
    });
    $set->addListener(ItemCountEvent::NAME, function($event) {
      $event->add(4);
    });
    $total = count($set);
    $this->assertEquals(6, $total);
  }

}
