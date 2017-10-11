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

class Query
{
    protected $fieldsMap = null;

    protected $fields = [];

    public function __construct($fieldsMap = null)
    {
        $this->fieldsMap = $fieldsMap;
    }

    public function field($fieldName)
    {
        $fieldObject = new Field($fieldName, $this);
        $this->fields[$fieldName] = $fieldObject;
        return $fieldObject;
    }

    public function get()
    {
        return [];
    }
}