<?php
/**
 *
 * PHP version >= 7.1
 *
 * @package andydune/mongo-query
 * @link  https://github.com/AndyDune/MongoQuery for the canonical source repository
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrey Ryzhov  <info@rznw.ru>
 * @copyright 2017 Andrey Ryzhov
 *
 */


namespace AndyDune\MongoQuery\Operator;


class Limit extends OperatorAbstract
{
    public function __invoke(...$params)
    {
        $limit = $params[0] ?? 1;
        $this->query->mergeFindOptions(['limit' => $limit]);
        return null;
    }
}