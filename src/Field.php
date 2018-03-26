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


/**
 * Class Field
 * @package AndyDune\MongoQuery
 * @deprecated
 */
class Field
{
    /**
     * @var Query
     */
    protected $query;

    protected $name;

    protected $link = 'and';

    protected $sourceData = [];

    /**
     * Field constructor.
     *
     * @param string $fieldName
     * @param Query $query
     */
    public function __construct($fieldName, Query $query)
    {
        $this->query = $query;
        $this->name = $fieldName;
    }

    public function in(...$params)
    {
        if (!count($params)) {
            return $this;
        }
        $data = [];
        foreach($params as $key => $param) {
            if (!is_array($param)) {
                $params[$key] = [$param];
            }
        }
        $data = array_merge($data, ...$params);
        $this->sourceData[] = [
            'data' => ['$in' => $data],
            'link' => $this->link
        ];
        return $this;
    }

    public function getAssembledData()
    {
        $results = [];
        $count = count($this->sourceData);
        if ($count == 1) {
            return current($this->sourceData)['data'];
        }
        //
        foreach ($this->sourceData as $row) {
            $results = $row;
        }
        return $results;
    }

    public function get()
    {
        return $this->query->get();
    }
}