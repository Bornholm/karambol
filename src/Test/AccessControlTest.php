<?php

namespace Karambol\Test;

use Karambol\AccessControl\Parser\ResourceSelectorTokenizer;
use Karambol\AccessControl\Parser\ResourceSelectorParser;
use Karambol\AccessControl\ResourceSelector;
use Karambol\AccessControl\ResourceOwner;
use Karambol\AccessControl\Resource;

class AccessControlTest extends \PHPUnit_Framework_TestCase
{

  public function testSelectorTokenizerValidExpressions() {

    $tokenizer = new ResourceSelectorTokenizer();

    $selector = 'post1[15,16]';
    $tokens = $tokenizer->tokenize($selector);

    $this->assertCount(1, $tokens);
    $this->assertArraySubset([ 0 => [ 'token' => ResourceSelectorTokenizer::TOKEN_RESOURCE, 'type' => 'post1',  'references' => ['15','16']]], $tokens);

    $selector = 'post2[15]@self';
    $tokens = $tokenizer->tokenize($selector);

    $this->assertArraySubset([ 0 => [ 'token' => ResourceSelectorTokenizer::TOKEN_RESOURCE, 'type' => 'post2',  'references' => ['15']]], $tokens);
    $this->assertArraySubset([ 1 => [ 'token' => ResourceSelectorTokenizer::TOKEN_OWNER,  'references' => ['self']]], $tokens);
    $this->assertCount(2, $tokens);

    $selector = 'post3[15]@[9,523]';
    $tokens = $tokenizer->tokenize($selector);

    $this->assertArraySubset([ 0 => [ 'token' => ResourceSelectorTokenizer::TOKEN_RESOURCE, 'type' => 'post3', 'references' => ['15']]], $tokens);
    $this->assertArraySubset([ 1 => [ 'token' => ResourceSelectorTokenizer::TOKEN_OWNER, 'references' => ['9', '523']]], $tokens);
    $this->assertCount(2, $tokens);

    $selector = 'post4@self';
    $tokens = $tokenizer->tokenize($selector);

    $this->assertArraySubset([ 0 => [ 'token' => ResourceSelectorTokenizer::TOKEN_RESOURCE, 'type' => 'post4']], $tokens);
    $this->assertArraySubset([ 1 => [ 'token' => ResourceSelectorTokenizer::TOKEN_OWNER,  'references' => ['self']]], $tokens);
    $this->assertCount(2, $tokens);

    $selector = 'post5[*]';
    $tokens = $tokenizer->tokenize($selector);

    $this->assertArraySubset([ 0 => [ 'token' => ResourceSelectorTokenizer::TOKEN_RESOURCE, 'type' => 'post5', 'references' => ['*']]], $tokens);
    $this->assertCount(1, $tokens);

  }

  public function testSelectorTokenizerInvalidExpression() {

    $this->setExpectedException('Karambol\AccessControl\Parser\TokenizerException', 'Invalid character "[" found at position 0 while tokenizing selector "[sdfsdf]" !');

    $tokenizer = new ResourceSelectorTokenizer();

    $selector = '[sdfsdf]';
    $tokens = $tokenizer->tokenize($selector);

  }

  public function testSelectorTokenizerInvalidExpression2() {

    $this->setExpectedException('Karambol\AccessControl\Parser\TokenizerException');

    $tokenizer = new ResourceSelectorTokenizer();

    $selector = 'post4@self[123]';
    $tokens = $tokenizer->tokenize($selector);

  }

  public function testResourceSelectorParser() {

    $parser = new ResourceSelectorParser();
    $selector = $parser->parse('post3[id1,id-2,ID3,id_5]@[owner,self,owner_2]');

    $this->assertEquals('post3', $selector->getResourceType());
    $this->assertArraySubset(['id1', 'id-2', 'ID3', 'id_5'], $selector->getResourceReferences());
    $this->assertArraySubset(['owner', 'self', 'owner_2'], $selector->getOwnerReferences());
    $this->assertCount(4, $selector->getResourceReferences());
    $this->assertCount(3, $selector->getOwnerReferences());

  }

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

  }

}
