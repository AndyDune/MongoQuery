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