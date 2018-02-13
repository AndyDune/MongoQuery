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
use AndyDune\MongoQuery\Query;
use PHPUnit\Framework\TestCase;


class DeleteTest extends TestCase
{
    public function testDeleteMany()
    {

        $queryGreater = new Query();
        $queryGreater->field('price')->gt(100);

        $queryLess = new Query();
        $queryLess->field('price')->lt(50);

        $mongo =  new \MongoDB\Client();
        $collection = $mongo->selectDatabase('test')->selectCollection('test');
        $collection->deleteMany(['for travis fix php 7.1' => null]);
        $collection->insertOne(['price' => 110]);
        $collection->insertOne(['price' => 120]);
        $collection->insertOne(['price' => 90]);
        $collection->insertOne(['price' => 51]);
        $collection->insertOne(['price' => '30']);
        $collection->insertOne(['price' => 45]);

        $queryGreater->deleteMany($collection);

        $rows = $collection->find([])->toArray();
        $this->assertCount(4, $rows);

        $queryLess->deleteMany($collection);

        $rows = $collection->find([])->toArray();
        $this->assertCount(3, $rows);
    }
}