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

        $query = new Query();
        $dataNot = $query->field('code')->not()->in('1', [2, 3])->get();

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

        $results = $collection->find($dataNot)->toArray();
        $this->assertCount(1, $results);

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

        $query = new Query();
        $dataNot = $query->field('price')->not()->between(10, 100)->get();

        $mongo =  new \MongoDB\Client();
        $collection = $mongo->selectDatabase('test')->selectCollection('test');
        $collection->deleteMany([]);
        $collection->insertOne(['price' => 20]);
        $collection->insertOne(['price' => 5]);
        $collection->insertOne(['price' => '30']);
        $collection->insertOne(['price' => 105]);

        // price = '30' - out of type

        $results = $collection->find($data)->toArray();
        $this->assertCount(1, $results);

        $results = $collection->find($dataNot)->toArray();
        $this->assertCount(2, $results);
    }

    public function testLessThanGreaterThan()
    {
        $query = new Query();
        $dataGreater = $query->field('price')->gt(100)->get();

        $query = new Query();
        $dataLess = $query->field('price')->lt(50)->get();

        $mongo =  new \MongoDB\Client();
        $collection = $mongo->selectDatabase('test')->selectCollection('test');
        $collection->deleteMany([]);
        $collection->insertOne(['price' => 110]);
        $collection->insertOne(['price' => 120]);
        $collection->insertOne(['price' => 90]);
        $collection->insertOne(['price' => '30']);
        $collection->insertOne(['price' => 45]);

        $results = $collection->find($dataGreater)->toArray();
        $this->assertCount(2, $results);

        $results = $collection->find($dataLess)->toArray();
        $this->assertCount(1, $results);
    }

    public function testNestedQuery()
    {

        $mongo =  new \MongoDB\Client();
        $collection = $mongo->selectDatabase('test')->selectCollection('test');
        $collection->deleteMany([]);
        $collection->insertOne(['price' => 110]);
        $collection->insertOne(['price' => 10]);
        $collection->insertOne(['price' => 90]);
        $collection->insertOne(['price' => 45]);

        $query = new Query();
        $data = $query->field('price')->gt(80)->get();

        $results = $collection->find($data)->toArray();
        $this->assertCount(2, $results);

        $queryAdd = new Query();
        $queryAdd->field('price')->lt(100);
        $data = $query->addQuery($queryAdd)->get();

        $results = $collection->find($data)->toArray();
        $this->assertCount(1, $results);

        $query->setFieldsJoinLogic('or');
        $results = $collection->find($query->get())->toArray();
        $this->assertCount(4, $results);


        $query = new Query();
        $query->field('price')->gt(100)->get();

        $queryAdd = new Query();
        $query->addQuery($queryAdd);
        $queryAdd->field('price')->lt(50);

        $query->setFieldsJoinLogic('nor');
        $results = $collection->find($query->get())->toArray();
        $this->assertCount(1, $results);

    }


}