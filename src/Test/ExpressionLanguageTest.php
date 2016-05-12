<?php

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class ExpressionLanguageTest extends PHPUnit_Framework_TestCase
{

    public function testExpressionLanguage()
    {
      $language = new ExpressionLanguage();
      var_dump($language->evaluate('1 + 2'));
    }

}
