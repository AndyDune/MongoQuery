# MongoQuery

[![Build Status](https://travis-ci.org/AndyDune/MongoQuery.svg?branch=master)](https://travis-ci.org/AndyDune/MongoQuery)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Packagist Version](https://img.shields.io/packagist/v/andydune/mongo-query.svg?style=flat-square)](https://packagist.org/packages/andydune/mongo-query)
[![Total Downloads](https://img.shields.io/packagist/dt/andydune/mongo-query.svg?style=flat-square)](https://packagist.org/packages/andydune/mongo-query)


Add beauty to momgodb query arrays. Less errors, less brackets, more understanding. It is not ORM nr ODM, it's only builder. So you may feel free to use it standalone or with any orm like [mongolid](https://github.com/leroy-merlin-br/mongolid)

## What is beauty
Originally it looks like:
```php
$collection = (new MongoDB\Client)->shop->tobacco;
$cursor = $collection->find(['price' => ['$lt' => 1000]]);

$collection = (new MongoDB\Client)->shop->tobacco;
$cursor = $collection->find(['type' => ['$in' => ['virginia', 'latakia']]]); // 3 brackets at once
```
MongoQuery change it:
```php
$collection = (new MongoDB\Client)->shop->tobacco;
$cursor = $collection->find((new Query)->field('price')->lessThan(1000)->get());
// or
$cursor = (new Query)->field('price')->lessThan(1000)->find($collection);


$collection = (new MongoDB\Client)->test->tobacco;
$cursor = $collection->find((new Query)->field('type')->in('virginia', 'latakia')->get());
// or 
$cursor = (new Query)->field('type')->in('virginia', 'latakia')->find($collection)

```


Installation
------------

Installation using composer:

```
composer require andydune/mongo-query
```
Or if composer didn't install globally:
```
php composer.phar require andydune/mongo-query
```
Or edit your `composer.json`:
```
"require" : {
     "andydune/pipeline": "^0"
}

```
And execute command:
```
php composer.phar update
```

## Execution

You can use methods `find` or `findOne`: 

```php
$query = new Query();
$query->field('price')->between(10, 100);

$mongo =  new \MongoDB\Client();
$collection = $mongo->selectDatabase('shop')->selectCollection('tobacco');
$result = $query->find($collection, ['sort' => ['name' => 1]])->toArray();

$result = $query->findOne($collection, ['sort' => ['name' => 1]]);
```

## Elements of Beauty

### Not

Operator make negative condition for right next operator in chain.

*Important!* It is not suitable for all operators.

### In

Original:
```php
$collection = (new MongoDB\Client)->base->tobacco;
$cursor = $collection->find(['type' => ['$in' => ['virginia', 'latakia']]]);
// if not
$cursor = $collection->find(['type' => ['$not' => ['$in' => ['virginia', 'latakia']]]]); // to many brackets
```

More beauty
```php
use AndyDune\MongoQuery\Query;

$collection = (new MongoDB\Client)->test->tobacco;
$cursor = $collection->find((new Query)->field('type')->in('virginia', 'latakia')->get());
//or 
$cursor = $collection->find((new Query)->field('type')->in(['virginia', 'latakia'])->get());
//or 
$cursor = $collection->find((new Query)->field('type')->in(['virginia'], 'latakia')->get());
```
Operation can be used with `not` modifier.
```php
$cursor = $collection->find((new Query)->field('type')->not()->in('virginia', 'latakia')->get());
```

### Between

Original:
```php
$collection = (new MongoDB\Client)->base->tobacco;
$cursor = $collection->find(
['$and' => [
    ['price' => ['$gt' => 10]],
    ['price' => ['$lt' => 100]]
]]);
```

More beauty
```php
$collection = (new MongoDB\Client)->test->tobacco;
$cursor = $collection->find(
(new Query)->field('price')->between->(10, 100)->get()
);
```

Operation can be used with `not` modifier.
```php
(new Query)->field('price')->not()->between->(10, 100)->get()
```

### eq 

Matches values that are equal to a specified value.

```php
(new Query)->field('price')->eq->(10)->get(); // ['price' => ['$eq' => 10]]

```

Operation can not be used with `not` modifier. It is special method `ne`

### ne 

Matches all values that are not equal to a specified value.

```php
(new Query)->field('price')->ne->(10)->get(); // ['price' => ['$ne' => 10]]

```

Operation can not be used with `not` modifier.


### gt and lt  

Operators for `$gt` and `$lt` comparision. 

```php
(new Query)->field('price')->lt->(10)->get();

(new Query)->field('price')->gt->(100)->get();
```


Operation can not be used with `not` modifier.

## Nested queries

Query objects can be the conditions of new query. There is method `addQuery` for this.

```php
$query = new Query();
$query->field('price')->gt(80);

$queryAdd = new Query();
$queryAdd->field('price')->lt(100);

$data = $query->addQuery($queryAdd)->get();
``` 

## Data type correction

It's important to use as parameters for query data with type same as data in collection.
So `1 != '1'`.

`Query` constructor can receive array with meta description for fields.

```php
$query = new Query([
   'price' => 'int',
   'type' => 'string',
]);
$queryArray = $queryAdd->field('price')->gt(false)->get();

// after all:
$queryArray = [
   'price' => ['$gt' => 0] // (int)false -> 0
];

``` 
