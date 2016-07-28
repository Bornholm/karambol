<?php

namespace Karambol\Test\AccessControl;

use Karambol\AccessControl\Parser\ResourceSelectorTokenizer;
use Karambol\AccessControl\Parser\ResourceSelectorParser;
use Karambol\AccessControl\ResourceSelector;
use Karambol\AccessControl\ResourceOwner;
use Karambol\AccessControl\Resource;

class ResourceSelectorTest extends \PHPUnit_Framework_TestCase
{

  public function testResourceSelectorMatch() {

    $selector = new ResourceSelector('post', ['id1'], ['self']);
    $owner = new ResourceOwner('owner_2');
    $resource = new Resource('post', 'id1', 'owner_2');

    $match = $selector->match($resource, $owner);

    $this->assertTrue($match);

    $selector = new ResourceSelector('post', ['id2'], ['self']);
    $owner = new ResourceOwner('owner_2');
    $resource = new Resource('post', 'id1', 'owner_2');

    $match = $selector->match($resource, $owner);

    $this->assertFalse($match);

    $selector = new ResourceSelector('post', ['id1'], ['owner_2']);
    $owner = new ResourceOwner('owner_2');
    $resource = new Resource('post', 'id1', 'owner_2');

    $match = $selector->match($resource, $owner);

    $this->assertTrue($match);

    $selector = new ResourceSelector('*', ['id1'], ['owner_2']);
    $owner = new ResourceOwner('owner_2');
    $resource = new Resource('post1', 'id1', 'owner_2');

    $match = $selector->match($resource, $owner);

    $this->assertTrue($match);

    $selector = new ResourceSelector('*', ['*']);
    $owner = new ResourceOwner('3');
    $resource = new Resource('url', 'url[/admin]');

    $match = $selector->match($resource, $owner);

    $this->assertTrue($match);

  }

}
