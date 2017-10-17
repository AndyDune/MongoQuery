<?php
/**
 * ----------------------------------------------
 * | Author: Андрей Рыжов (Dune) <info@rznw.ru>  |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 17.10.2017                               |
 * -----------------------------------------------
 *
 */


namespace AndyDune\MongoQuery;


class FieldTypeCorrector
{
    protected $fieldsMap;

    protected $typeAliasesToCorrectMethod = [
        'int'     => 'correctInteger',
        'integer' => 'correctInteger',
        'i'       => 'correctInteger',
        'string'  => 'correctString',
        'str'     => 'correctString',
        's'       => 'correctString',
        'bool'    => 'correctBoolean',
        'boolean' => 'correctBoolean',
        'b'       => 'correctBoolean',
        //'createdAtTimestamp' => 'correctDateTime',
        //'updatedAtTimestamp' => 'correctDateTime',
    ];

    public function __construct($fieldMap)
    {
        $this->fieldsMap = $fieldMap ?? [];
    }


    public function correct($fieldName, $value)
    {
        if (!array_key_exists($fieldName, $this->fieldsMap)) {
            return $value;
        }

        if (!array_key_exists($this->fieldsMap[$fieldName], $this->typeAliasesToCorrectMethod)) {
            return $value;
        }

        return call_user_func([$this, $this->typeAliasesToCorrectMethod[$this->fieldsMap[$fieldName]]], $value);
    }


    protected function correctInteger($value)
    {
        return (int)$value;
    }

    protected function correctString($value)
    {
        return (string)$value;
    }

    protected function correctBoolean($value)
    {
        return (bool)$value;
    }

}