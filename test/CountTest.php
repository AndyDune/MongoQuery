<?php
/**
 * ----------------------------------------------
 * | Author: Andrey Ryzhov (Dune) <info@rznw.ru> |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 14.02.2018                            |
 * -----------------------------------------------
 *
 */


namespace AndyDune\MongoQueryTest;
use AndyDune\MongoQuery\Query;
use PHPUnit\Framework\TestCase;


class CountTest extends TestCase
{
    public function testDeleteMany()
    {

        $queryGreater = new Query();
        $queryGreater->field('price')->gt(100);

        $queryLess = new Query();
        $queryLess->field('price')->lt(50);

        $mongo =  new \MongoDB\Client();
        $collection = $mongo->selectDatabase('test')->selectCollection('test');
        $collection->deleteMany([]);
        $collection->insertOne(['price' => 110]);
        $collection->insertOne(['price' => 120]);
        $collection->insertOne(['price' => 90]);
        $collection->insertOne(['price' => 51]);
        $collection->insertOne(['price' => '30']);
        $collection->insertOne(['price' => 45]);

        $res = $queryGreater->count($collection);
        $this->assertEquals(2, $res);


        $res = $queryLess->count($collection);
        $this->assertEquals(1, $res);
    }

}