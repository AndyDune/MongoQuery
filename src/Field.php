<?php
/**
 * ----------------------------------------------
 * | Author: Андрей Рыжов (Dune) <info@rznw.ru>  |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 10.10.2017                            |
 * -----------------------------------------------
 *
 */


namespace AndyDune\MongoQuery;


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