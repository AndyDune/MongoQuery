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