<?php
/**
 * ----------------------------------------------
 * | Author: Андрей Рыжов (Dune) <info@rznw.ru>  |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 12.10.2017                               |
 * -----------------------------------------------
 *
 */


namespace AndyDune\MongoQuery\Operator;


class LessThan extends OperatorAbstract
{
    public function __invoke(...$params)
    {
        if (count($params) != 1) {
            return false;
        }
        return [$this->fieldName => ['$lt' => $params[0]]];
    }

}