<?php
/**
 * Add beauty to momgodb query arrays. Less errors, less brackets, more understanding. It is not ORM nor ODM, it's only builder.
 *
 * PHP version >= 7.1
 *
 *
 * @package andydune/mongo-query
 * @link  https://github.com/AndyDune/MongoQuery for the canonical source repository
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrey Ryzhov  <info@rznw.ru>
 * @copyright 2018 Andrey Ryzhov
 *
 */

namespace AndyDune\MongoQuery;
use AndyDune\MongoOdm\DocumentAbstract;

class FieldTypeOdmCorrector
{
    /**
     * @var DocumentAbstract
     */
    protected $odm;

    public function __construct(DocumentAbstract $odm)
    {
        $this->odm = $odm;
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
        return $value;
    }
}