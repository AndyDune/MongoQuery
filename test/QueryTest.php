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
        $this->assertInstanceOf(Query::class, $field);
        $result = $field->get();
        $this->assertEquals(['$and' => []], $result);
    }

    public function testIn()
    {
        $wait = ['$and' => [
                ['code' => ['$in' => ['1', 2, 3]]]
            ]
        ];
        $query = new Query();
        $data = $query->field('code')->in(['1', 2, 3])->get();
        $this->assertEquals($wait, $data);

        $query = new Query();
        $data = $query->field('code')->in('1', 2, 3)->get();
        $this->assertEquals($wait, $data);

        $query = new Query();
        $data = $query->field('code')->in(['1', 2], 3)->get();
        $this->assertEquals($wait, $data);

        $query = new Query();
        $data = $query->field('code')->in('1', [2, 3])->get();
        $this->assertEquals($wait, $data);

        $mongo =  new \MongoDB\Client();
        $collection = $mongo->selectDatabase('test')->selectCollection('test');
        $collection->deleteMany([]);
        $collection->insertOne(['code' => 1]);
        $collection->insertOne(['code' => 2]);
        $collection->insertOne(['code' => 3]);
        $results = $collection->find($data)->toArray();
        $this->assertCount(2, $results);
        $collection->insertOne(['code' => '1']);
        $results = $collection->find($data)->toArray();
        $this->assertCount(3, $results);
    }

    public function testBetween()
    {
        $wait = [
            '$and' =>[
                ['$and' => [
                        ['price' => ['$gt' => 10]],
                        ['price' => ['$lt' => 100]]
                    ]
                ]
            ]
        ];

        $query = new Query();
        $data = $query->field('price')->between(10, 100)->get();
        $this->assertEquals($wait, $data);

        $mongo =  new \MongoDB\Client();
        $collection = $mongo->selectDatabase('test')->selectCollection('test');
        $collection->deleteMany([]);
        $collection->insertOne(['price' => 20]);
        $collection->insertOne(['price' => 5]);
        $collection->insertOne(['price' => '30']);
        $collection->insertOne(['price' => 105]);

        $results = $collection->find($data)->toArray();
        $this->assertCount(1, $results);
    }
}