<?php
/**
 *
 * PHP version 7.0 and 7.1
 *
 * @package andydune/mongo-query
 * @link  https://github.com/AndyDune/MongoQuery for the canonical source repository
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrey Ryzhov  <info@rznw.ru>
 * @copyright 2018 Andrey Ryzhov
 *
 */


namespace AndyDune\MongoQueryTest;
use AndyDune\MongoQuery\FieldTypeCorrector;
use AndyDune\MongoQuery\Query;
use PHPUnit\Framework\TestCase;

class FieldTypeCorrectorTest extends TestCase
{
    public function testFieldMethod()
    {
        $corrector = new FieldTypeCorrector(['number' => 'i']);

        $result = $corrector->correct('number', '123');
        $this->assertTrue(123 === $result);

        $result = $corrector->correct('number_not_corected', '123');
        $this->assertFalse(123 === $result);

        $result = $corrector->correct('number', 'frog');
        $this->assertTrue(0 === $result);


        $result = $corrector->correct('number', null);
        $this->assertTrue(0 === $result);

        $result = $corrector->correct('number', true);
        $this->assertTrue(1 === $result);

        $result = $corrector->correct('number', false);
        $this->assertTrue(0 === $result);

        $result = $corrector->correct('number', []);
        $this->assertTrue(0 === $result);

        $result = $corrector->correct('number', '');
        $this->assertTrue(0 === $result);


        // don touche next fields
        $result = $corrector->correct('string', 'frog');
        $this->assertTrue('frog' === $result);
    }
}