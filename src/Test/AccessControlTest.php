<?php

namespace Karambol\Test;

use Karambol\AccessControl\Parser\ResourceCriteriaTokenizer;

class AccessControlTest extends \PHPUnit_Framework_TestCase
{

  public function testCriteriaTokenizerValidExpressions() {

    $tokenizer = new ResourceCriteriaTokenizer();

    $criteria = 'post1[15,16]';
    $tokens = $tokenizer->tokenize($criteria);

    $this->assertCount(1, $tokens);
    $this->assertArraySubset([ 0 => [ 'token' => ResourceCriteriaTokenizer::TOKEN_RESOURCE, 'type' => 'post1',  'references' => ['15','16']]], $tokens);

    $criteria = 'post2[15]@self';
    $tokens = $tokenizer->tokenize($criteria);

    $this->assertArraySubset([ 0 => [ 'token' => ResourceCriteriaTokenizer::TOKEN_RESOURCE, 'type' => 'post2',  'references' => ['15']]], $tokens);
    $this->assertArraySubset([ 1 => [ 'token' => ResourceCriteriaTokenizer::TOKEN_OWNER,  'references' => ['self']]], $tokens);
    $this->assertCount(2, $tokens);

    $criteria = 'post3[15]@[9,523]';
    $tokens = $tokenizer->tokenize($criteria);

    $this->assertArraySubset([ 0 => [ 'token' => ResourceCriteriaTokenizer::TOKEN_RESOURCE, 'type' => 'post3', 'references' => ['15']]], $tokens);
    $this->assertArraySubset([ 1 => [ 'token' => ResourceCriteriaTokenizer::TOKEN_OWNER, 'references' => ['9', '523']]], $tokens);
    $this->assertCount(2, $tokens);

    $criteria = 'post4@self';
    $tokens = $tokenizer->tokenize($criteria);

    $this->assertArraySubset([ 0 => [ 'token' => ResourceCriteriaTokenizer::TOKEN_RESOURCE, 'type' => 'post4']], $tokens);
    $this->assertArraySubset([ 1 => [ 'token' => ResourceCriteriaTokenizer::TOKEN_OWNER,  'references' => ['self']]], $tokens);
    $this->assertCount(2, $tokens);

  }

  public function testCriteriaTokenizerInvalidExpression() {

    $this->setExpectedException('Karambol\AccessControl\Parser\TokenizerException', 'Invalid character "[" found at position 0 while tokenizing criteria "[sdfsdf]" !');

    $tokenizer = new ResourceCriteriaTokenizer();

    $criteria = '[sdfsdf]';
    $tokens = $tokenizer->tokenize($criteria);

  }

}
