<?php

namespace Karambol\Test\AccessControl;

use Karambol\AccessControl\Parser\ResourceSelectorTokenizer;
use Karambol\AccessControl\Parser\ResourceSelectorParser;
use Karambol\AccessControl\ResourceSelector;
use Karambol\AccessControl\Resource;

class ResourceSelectorParserTest extends \PHPUnit_Framework_TestCase
{

  public function testResourceSelectorParser() {

    $parser = new ResourceSelectorParser();
    $selector = $parser->parse('post3[id1,id-2,ID3,id_5]');

    $this->assertEquals('post3', $selector->getResourceType());
    $this->assertArraySubset(['id1', 'id-2', 'ID3', 'id_5'], $selector->getResourceReferences());
    $this->assertCount(4, $selector->getResourceReferences());

  }

}
