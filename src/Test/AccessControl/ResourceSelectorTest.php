<?php

namespace Karambol\Test\AccessControl;

use Karambol\AccessControl\Parser\ResourceSelectorTokenizer;
use Karambol\AccessControl\Parser\ResourceSelectorParser;
use Karambol\AccessControl\ResourceSelector;
use Karambol\AccessControl\Resource;

class ResourceSelectorTest extends \PHPUnit_Framework_TestCase
{

  public function testResourceSelectorMatch() {

    $selector = new ResourceSelector('post', ['id1']);
    $resource = new Resource('post', 'id1');

    $match = $selector->matches($resource);

    $this->assertTrue($match);

    $selector = new ResourceSelector('post', ['id2']);
    $resource = new Resource('post', 'id1');

    $match = $selector->matches($resource);

    $this->assertFalse($match);

    $selector = new ResourceSelector('post', ['id1']);
    $resource = new Resource('post', 'id1');

    $match = $selector->matches($resource);

    $this->assertTrue($match);

    $selector = new ResourceSelector('*', ['id1']);
    $resource = new Resource('post1', 'id1');

    $match = $selector->matches($resource);

    $this->assertTrue($match);

    $selector = new ResourceSelector('*', ['*']);
    $resource = new Resource('url', 'url[/admin]');

    $match = $selector->matches($resource);

    $this->assertTrue($match);

  }

}
