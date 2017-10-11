<?php
/**
 * ----------------------------------------------
 * | Author: Андрей Рыжов (Dune) <info@rznw.ru>  |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 11.10.2017                               |
 * -----------------------------------------------
 *
 */

namespace AndyDune\MongoQueryTest;
use AndyDune\MongoQuery\Field;
use AndyDune\MongoQuery\Query;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{
    public function testFieldMethod()
    {
        $query = new Query();
        $field = $query->field('name');
        $this->assertInstanceOf(Field::class, $field);
        $result = $field->get();
        $this->assertEquals([], $result);
    }
}