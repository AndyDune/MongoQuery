<?php
/**
 *
 * PHP version >= 7.1
 *
 * @package andydune/mongo-query
 * @link  https://github.com/AndyDune/MongoQuery for the canonical source repository
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrey Ryzhov  <info@rznw.ru>
 * @copyright 2017 Andrey Ryzhov
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