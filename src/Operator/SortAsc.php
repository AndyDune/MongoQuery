<?php
/**
 * ----------------------------------------------
 * | Author: Андрей Рыжов (Dune) <info@rznw.ru>  |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 09.11.2017                               |
 * -----------------------------------------------
 *
 */


namespace AndyDune\MongoQuery\Operator;


class SortAsc extends OperatorAbstract
{
    protected $sortOrder = 1;

    public function __invoke(...$params)
    {
        $field = $params[0] ?? $this->fieldName;
        $sort = $this->query->getFindOptions()['sort'] ?? [];
        $sort[$field] = $this->sortOrder;
        $this->query->mergeFindOptions(['sort' => $sort]);
        return null;
    }
}