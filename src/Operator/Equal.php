<?php
/**
 *
 * PHP version 7.0 and 7.1
 *
 * @package andydune/mongo-query
 * @link  https://github.com/AndyDune/MongoQuery for the canonical source repository
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrey Ryzhov  <info@rznw.ru>
 * @copyright 2017 Andrey Ryzhov
 *
 */


namespace AndyDune\MongoQuery\Operator;


class Equal extends OperatorAbstract
{
    public function __invoke(...$params)
    {
        if (count($params) != 1) {
            return false;
        }
        return [$this->fieldName => ['$eq' => $this->query->getFieldsCorrector()->correct($this->fieldName, $params[0])]];
    }

}