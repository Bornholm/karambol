<?php

namespace Karambol\Test;

use Karambol\VirtualSet\ItemMatcher;
use Karambol\Test\VirtualSetTest;

class ItemMatcherTest extends \PHPUnit_Framework_TestCase
{

  public function testItemMatcherBasic()
  {

    $criteria = ['foo' => 'bar'];
    $matcher = new ItemMatcher($criteria);

    $item = ['foo' => 'bar'];
    $result = $matcher->matches($item);
    $this->assertEquals(true, $result);

    $item = ['foo' => 'bla'];
    $result = $matcher->matches($item);
    $this->assertEquals(false, $result);

    $item = new \stdClass();
    $item->foo = 'bar';
    $result = $matcher->matches($item);
    $this->assertEquals(true, $result);

    $item = new VirtualSetTest\FooBarGet();
    $result = $matcher->matches($item);
    $this->assertEquals(true, $result);

    $item = new VirtualSetTest\FooBarHas();
    $result = $matcher->matches($item);
    $this->assertEquals(true, $result);

  }

  public function testItemMatcherNestedProperty()
  {

    $criteria = ['foo.bar' =>  'doh'];
    $matcher = new ItemMatcher($criteria);

    $item = ['foo' => 'bar'];
    $result = $matcher->matches($item);
    $this->assertEquals(false, $result);

    $item = new \stdClass();
    $item->foo = ['bar' => 'doh'];
    $result = $matcher->matches($item);
    $this->assertEquals(true, $result);

  }

}

namespace Karambol\Test\VirtualSetTest;

class FooBarGet {
  public function getFoo() {
    return 'bar';
  }
}

class FooBarHas {
  public function hasFoo() {
    return 'bar';
  }
}
