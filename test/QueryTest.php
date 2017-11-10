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
        $this->assertEquals([], $result);
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

        $queryNot = new Query();
        $dataNot = $queryNot->field('price')->not()->between(10, 100)->get();

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

        // Alternative method:
        $resultsAlter = $query->find($collection, ['sort' => ['price' => 1]])->toArray();
        $this->assertEquals($results, $resultsAlter);

        $results = $collection->find($dataNot)->toArray();
        $this->assertCount(2, $results);

        // with fields corrector:

        $collection->insertOne(['price' => 60]);
        $collection->insertOne(['price' => 70]);

        $query = new Query();
        $data = $query->field('price')->between('50', '100')->get();
        $results = $collection->find($data)->toArray();
        $this->assertCount(0, $results);

        $query = new Query(['price' => 'int']);
        $data = $query->field('price')->between('50', '100')->get();
        $results = $collection->find($data)->toArray();
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


    public function testEqualQuery()
    {

        $mongo =  new \MongoDB\Client();
        $collection = $mongo->selectDatabase('test')->selectCollection('test');
        $collection->deleteMany([]);
        $collection->insertOne(['price' => 10]);
        $collection->insertOne(['price' => 90]);

        $query = new Query();
        $data = $query->field('price')->eq(80)->get();
        $results = $collection->find($data)->toArray();
        $this->assertCount(0, $results);

        $query = new Query();
        $data = $query->field('price')->eq(10)->get();
        $results = $collection->find($data)->toArray();
        $this->assertCount(1, $results);
        $this->assertEquals(10, $results[0]['price']);

        $query = new Query();
        $data = $query->field('price')->eq('10')->get();
        $results = $collection->find($data)->toArray();
        $this->assertCount(0, $results);

        $query = new Query(['price' => 'int']);
        $data = $query->field('price')->eq('10')->get();
        $results = $collection->find($data)->toArray();
        $this->assertCount(1, $results);
        $this->assertEquals(10, $results[0]['price']);

        $query = new Query();
        $data = $query->field('price')->ne(80)->get();
        $results = $collection->find($data)->toArray();
        $this->assertCount(2, $results);

        $query = new Query();
        $data = $query->field('price')->ne(10)->get();
        $results = $collection->find($data)->toArray();
        $this->assertCount(1, $results);
        $this->assertEquals(90, $results[0]['price']);

        $query = new Query();
        $data = $query->field('price')->ne('10')->get();
        $results = $collection->find($data)->toArray();
        $this->assertCount(2, $results);

        $query = new Query(['price' => 'int']);
        $data = $query->field('price')->ne('10')->get();
        $results = $collection->find($data)->toArray();
        $this->assertCount(1, $results);
        $this->assertEquals(90, $results[0]['price']);


    }

    public function testSortLimitOffset()
    {
        $mongo =  new \MongoDB\Client();
        $collection = $mongo->selectDatabase('test')->selectCollection('test');
        $collection->deleteMany([]);
        $collection->insertOne(['price' => 10]);
        $collection->insertOne(['price' => 20]);
        $collection->insertOne(['price' => 30]);
        $collection->insertOne(['price' => 40]);

        $query = new Query(['price' => 'int']);
        $result = $query->limit(2)->field('price')->sortAsc()->find($collection);
        $this->assertInstanceOf('MongoDb\Driver\Cursor', $result);
        $resultArray = $result->toArray();
        $this->assertCount(2, $resultArray);
        $this->assertEquals(10, $resultArray[0]['price']);
        $this->assertEquals(20, $resultArray[1]['price']);

        $query = new Query(['price' => 'int']);
        $result = $query->limit(2)->field('price')->sortDesc()->find($collection);
        $this->assertInstanceOf('MongoDb\Driver\Cursor', $result);
        $resultArray = $result->toArray();
        $this->assertCount(2, $resultArray);
        $this->assertEquals(40, $resultArray[0]['price']);
        $this->assertEquals(30, $resultArray[1]['price']);

        $query = new Query(['price' => 'int']);
        $result = $query->limit(1)->field('price')->sortDesc()->find($collection);
        $this->assertInstanceOf('MongoDb\Driver\Cursor', $result);
        $resultArray = $result->toArray();
        $this->assertCount(1, $resultArray);
        $this->assertEquals(40, $resultArray[0]['price']);


        $query = new Query(['price' => 'int']);
        $result = $query->limit(2)->skip(1)->field('price')->sortDesc()->find($collection);
        $this->assertInstanceOf('MongoDb\Driver\Cursor', $result);
        $resultArray = $result->toArray();
        $this->assertCount(2, $resultArray);
        $this->assertEquals(30, $resultArray[0]['price']);
        $this->assertEquals(20, $resultArray[1]['price']);

        $query = new Query(['price' => 'int']);
        $result = $query->limit(2)->skip(1)->sortAsc('price')->find($collection);
        $this->assertInstanceOf('MongoDb\Driver\Cursor', $result);
        $resultArray = $result->toArray();
        $this->assertCount(2, $resultArray);
        $this->assertEquals(20, $resultArray[0]['price']);
        $this->assertEquals(30, $resultArray[1]['price']);

        $query = new Query(['price' => 'int']);
        $result = $query->limit(2)->skip(2)->sortAsc('price')->find($collection);
        $this->assertInstanceOf('MongoDb\Driver\Cursor', $result);
        $resultArray = $result->toArray();
        $this->assertCount(2, $resultArray);
        $this->assertEquals(30, $resultArray[0]['price']);
        $this->assertEquals(40, $resultArray[1]['price']);


        $query = new Query(['price' => 'int']);
        $result = $query->limit(2)->skip(3)->sortAsc('price')->find($collection);
        $this->assertInstanceOf('MongoDb\Driver\Cursor', $result);
        $resultArray = $result->toArray();
        $this->assertCount(1, $resultArray);
        $this->assertEquals(40, $resultArray[0]['price']);

    }

}