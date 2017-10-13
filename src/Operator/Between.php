<?php
/**
 *
 * PHP version 7.0 and 7.1
 *
 * @package andydune/mongo-query
 * @link  https://github.com/AndyDune/MongoQuery for the canonical source repository
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrey Ryzhov  <info@rznw.ru>
 * @copyright 2017 Andrey Ryzhov
 *
 */


namespace AndyDune\MongoQuery\Operator;


class Between extends OperatorAbstract
{

    public function __invoke(...$params)
    {
        if (count($params) != 2) {
            return false;
        }
        if ($this->query->isNot()) {
            $this->query->not(false);
            return [
                '$or' => [
                    [$this->fieldName => ['$lt' => $params[0]]],
                    [$this->fieldName => ['$gt' => $params[1]]]
                ]
            ];
        }
        return ['$and' => [
            [$this->fieldName => ['$gt' => $params[0]]],
            [$this->fieldName => ['$lt' => $params[1]]]
        ]
        ];
    }

}