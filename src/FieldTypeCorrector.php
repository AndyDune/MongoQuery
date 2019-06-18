<?php
/**
 * Add beauty to momgodb query arrays. Less errors, less brackets, more understanding. It is not ORM nr ODM, it's only builder.
 *
 * PHP version >= 7.1
 *
 *
 * @package andydune/mongo-query
 * @link  https://github.com/AndyDune/MongoQuery for the canonical source repository
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrey Ryzhov  <info@rznw.ru>
 * @copyright 2017 Andrey Ryzhov
 *
 */

namespace AndyDune\MongoQuery;


use AndyDune\DateTime\DateTime;
use MongoDB\BSON\UTCDateTime;

class FieldTypeCorrector
{
    protected $fieldsMap;

    const TYPE_DATETIME = 'datetime';
    const TYPE_BOOLEAN  = 'boolean';
    const TYPE_INTEGER  = 'integer';
    const TYPE_STRING  = 'string';

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
        'datetime'       => 'correctDatetime',
        //'createdAtTimestamp' => 'correctDateTime',
        //'updatedAtTimestamp' => 'correctDateTime',
    ];

    public function __construct($fieldMap)
    {
        $this->fieldsMap = $fieldMap ?? [];
    }


    /**
     * Execute inside Operator instances.
     *
     * @param $fieldName name of filed in collection
     * @param $value
     * @return mixed
     */
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

    protected function correctDatetime($value)
    {
        if ($value instanceof UTCDateTime) {
            return $value;
        }

        if (is_integer($value)) {
            return new UTCDateTime($value * 1000);
        }

        if ($value instanceof DateTime) {
            $datetime = $value;
        } else if (in_array(substr($value, 0, 1), ['-', '+']))  {
            $datetime = new DateTime();
            $datetime->add($value);
        } else {
            $datetime = new DateTime($value);
        }

        return new UTCDateTime($datetime->getTimestamp() * 1000);
    }

    protected function correctBoolean($value)
    {
        return (bool)$value;
    }

}