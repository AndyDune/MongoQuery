<?php
/**
 * ----------------------------------------------
 * | Author: Андрей Рыжов (Dune) <info@rznw.ru>  |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 10.10.2017                               |
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

    /**
     * Field constructor.
     *
     * @param string $fieldName
     * @param Query $query
     */
    public function __construct($fieldName, Query $query)
    {
        $this->query = $query;
    }

    public function get()
    {
        return $this->query->get();
    }
}