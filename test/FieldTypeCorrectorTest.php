<?php
/**
 * ----------------------------------------------
 * | Author: Андрей Рыжов (Dune) <info@rznw.ru>  |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 23.10.2017                               |
 * -----------------------------------------------
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