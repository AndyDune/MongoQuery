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

use AndyDune\MongoQuery\Operator\{Between, In, Not, GreaterThan, LessThan};

class Query
{
    protected $fieldsMap = null;

    protected $fields = [];

    protected $currentField = '';

    protected $nextCallback = null;

    protected $isNot = false;

    protected $operators = [
        'in' => In::class,
        'between' => Between::class,
        'not' => Not::class,
        'gt' => GreaterThan::class,
        'lt' => LessThan::class,
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
        $result = $function(...$arguments);
        if ($result) {
            $this->fields[] = $this->executeNextCallBack($result);
        }

        return $this;
    }

    /**
     *
     *
     * @param bool $on on or off nex not.
     * @return $this
     */
    public function not($on = true)
    {
        $this->isNot = $on;
        return $this;
    }

    public function isNot()
    {
        return $this->isNot;
    }


    public function get()
    {
        return ['$and' => $this->fields];
    }

    public function setNextCallBack(callable $function)
    {
        $this->nextCallback = $function;
        return $this;
    }

    protected function executeNextCallBack($data)
    {
        if ($this->nextCallback) {
            $data = call_user_func_array($this->nextCallback, [$data]);
            $this->nextCallback = null;
        }

        return $data;
    }

}