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


use AndyDune\MongoQuery\Query;

abstract class OperatorAbstract
{
    /**
     * @var string
     */
    protected $fieldName;

    /**
     * @var Query
     */
    protected $query;

    public function __construct($fieldName, Query $query)
    {
        $this->query = $query;
        $this->fieldName = $fieldName;
    }
}