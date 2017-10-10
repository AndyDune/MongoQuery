# MongoQuery
Add beauty to momgodb query arrays.

## What is beauty
Originally it looks like:
```php
$collection = (new MongoDB\Client)->base->tobacco;
$cursor = $collection->find(['price' => ['$lt' => 1000]]);

$collection = (new MongoDB\Client)->test->tobacco;
$cursor = $collection->find(['type' => ['$in' => ['virginia', 'latakia']]]); // 3 brackets at once
```
MongoQuery change it:
```php
$collection = (new MongoDB\Client)->test->tobacco;
$cursor = $collection->find((new MongoQuery)->field('price')->lessThan(1000)->get());

$collection = (new MongoDB\Client)->test->tobacco;
$cursor = $collection->find((new MongoQuery)->field('price')->in('virginia', 'latakia')->get());

```
