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


class In extends OperatorAbstract
{
    public function __invoke(...$params)
    {
        if (!count($params)) {
            return null;
        }
        $data = [];
        foreach($params as $key => $param) {
            if (!is_array($param)) {
                $params[$key] = [$param];
            }
        }
        $data = array_merge($data, ...$params);
        $data = array_map((function($value) {
            return $this->query->getFieldsCorrector()->correct($this->fieldName, $value);
        })->bindTo($this), $data);
        if ($this->query->isNot()) {
            $this->query->not(false);
            return [$this->fieldName => ['$not' => ['$in' => $data]]];
        }
        return [$this->fieldName => ['$in' => $data]];
    }
}