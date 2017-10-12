<?php
/**
 * ----------------------------------------------
 * | Author: Андрей Рыжов (Dune) <info@rznw.ru>  |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * -----------------------------------------------
 *
 */
namespace AndyDune\MongoQuery;

use AndyDune\MongoQuery\Operator\Between;
use AndyDune\MongoQuery\Operator\In;

class Query
{
    protected $fieldsMap = null;

    protected $fields = [];

    protected $currentField = '';

    protected $operators = [
        'in' => In::class,
        'between' => Between::class
    ];

    public function __construct($fieldsMap = null)
    {
        $this->fieldsMap = $fieldsMap;
    }

    public function field($fieldName)
    {
        $this->currentField = $fieldName;
        return $this;
    }

    public function __call($name, $arguments)
    {
        if (!isset($this->operators[$name])) {
            throw new Exception('Operator is not exist. May be yet.');
        }
        $function = new $this->operators[$name]($this->currentField, $this);
        $this->fields[] = $function(...$arguments);
        return $this;
    }

    public function get()
    {
        return ['$and' => $this->fields];
    }
}