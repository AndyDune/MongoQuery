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


class In extends OperatorAbstract
{
    public function __invoke(...$params)
    {
        if (!count($params)) {
            return null;
        }
        $data = [];
        foreach($params as $key => $param) {
            if (!is_array($param)) {
                $params[$key] = [$param];
            }
        }
        $data = array_merge($data, ...$params);
        return [$this->fieldName => ['$in' => $data]];
    }
}