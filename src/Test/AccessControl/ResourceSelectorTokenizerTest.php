<?php

namespace Karambol\Test\AccessControl;

use Karambol\AccessControl\Parser\ResourceSelectorTokenizer;
use Karambol\AccessControl\Parser\ResourceSelectorParser;
use Karambol\AccessControl\ResourceSelector;
use Karambol\AccessControl\ResourceOwner;
use Karambol\AccessControl\Resource;

class ResourceSelectorTokenizerTest extends \PHPUnit_Framework_TestCase
{

  public function testSelectorTokenizerValidExpressions() {

    $tokenizer = new ResourceSelectorTokenizer();

    $selector = 'post1[15,16]';
    $tokens = $tokenizer->tokenize($selector);

    $this->assertCount(1, $tokens);
    $this->assertArraySubset([ 0 => [ 'token' => ResourceSelectorTokenizer::TOKEN_RESOURCE, 'type' => 'post1',  'references' => ['15','16']]], $tokens);

    $selector = 'post2[15]';
    $tokens = $tokenizer->tokenize($selector);

    $this->assertArraySubset([ 0 => [ 'token' => ResourceSelectorTokenizer::TOKEN_RESOURCE, 'type' => 'post2',  'references' => ['15']]], $tokens);
    $this->assertCount(1, $tokens);

    $selector = 'post3[15]';
    $tokens = $tokenizer->tokenize($selector);

    $this->assertArraySubset([ 0 => [ 'token' => ResourceSelectorTokenizer::TOKEN_RESOURCE, 'type' => 'post3', 'references' => ['15']]], $tokens);
    $this->assertCount(1, $tokens);

    $selector = 'post4';
    $tokens = $tokenizer->tokenize($selector);

    $this->assertArraySubset([ 0 => [ 'token' => ResourceSelectorTokenizer::TOKEN_RESOURCE, 'type' => 'post4']], $tokens);
    $this->assertCount(1, $tokens);

    $selector = 'post5[*]';
    $tokens = $tokenizer->tokenize($selector);

    $this->assertArraySubset([ 0 => [ 'token' => ResourceSelectorTokenizer::TOKEN_RESOURCE, 'type' => 'post5', 'references' => ['*']]], $tokens);
    $this->assertCount(1, $tokens);

    $selector = 'user.password[5]';
    $tokens = $tokenizer->tokenize($selector);

    $this->assertArraySubset([ 0 => [ 'token' => ResourceSelectorTokenizer::TOKEN_RESOURCE, 'type' => 'user', 'references' => ['5'], 'property' => 'password']], $tokens);
    $this->assertCount(1, $tokens);

  }

  public function testSelectorTokenizerInvalidExpression() {

    $this->setExpectedException('Karambol\AccessControl\Parser\TokenizerException', 'Invalid character "[" found at position 0 while tokenizing selector "[sdfsdf]" !');

    $tokenizer = new ResourceSelectorTokenizer();

    $selector = '[sdfsdf]';
    $tokens = $tokenizer->tokenize($selector);

  }

}
