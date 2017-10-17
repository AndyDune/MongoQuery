<?php
/**
 * Add beauty to momgodb query arrays. Less errors, less brackets, more understanding. It is not ORM nr ODM, it's only builder.
 *
 * PHP version 7.0 and 7.1
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

use AndyDune\MongoQuery\Operator\{Between, In, Not, GreaterThan, LessThan};

/**
 * @method $this between($value1, $value2)
 * @method $this in(...$values)
 * @method $this gt($value)
 * @method $this lt($value)
 */
class Query
{
    /**
     * @var FieldTypeCorrector
     */
    protected $fieldsCorrector;

    protected $fields = [];

    protected $currentField = '';

    protected $nextCallback = null;

    protected $isNot = false;

    protected $operators = [
        'in' => In::class,
        'between' => Between::class,
        'gt' => GreaterThan::class,
        'lt' => LessThan::class,
    ];

    protected $fieldsJoinLogic = '$and';

    public function __construct($fieldsMap = null)
    {
        $this->fieldsCorrector = new FieldTypeCorrector($fieldsMap);
    }

    /**
     * @return FieldTypeCorrector
     */
    public function getFieldsCorrector()
    {
        return $this->fieldsCorrector;
    }

    /**
     * Add collection field name to description with next operators.
     *
     * @param string $fieldName
     * @return $this
     */
    public function field($fieldName)
    {
        $this->currentField = $fieldName;
        return $this;
    }

    /**
     * Method names are in class operations var
     *
     * @param $name
     * @param $arguments
     * @return $this
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        if (!isset($this->operators[$name])) {
            throw new Exception('Operator is not exist. May be yet.');
        }
        $function = new $this->operators[$name]($this->currentField, $this);
        $result = $function(...$arguments);
        if ($result) {
            $this->fields[] = $this->executeNextCallBack($result);
        }

        return $this;
    }

    /**
     *
     *
     * @param bool $on on or off nex not.
     * @return $this
     */
    public function not($on = true)
    {
        $this->isNot = $on;
        return $this;
    }

    /**
     * Return flag negative next operation.
     *
     * @return bool
     */
    public function isNot()
    {
        return $this->isNot;
    }

    /**
     * @param Query $query
     * @return mixed
     */
    public function addQuery(Query $query)
    {
        $this->fields[] = $query;
        return $this;
    }

    /**
     * Describe logic for join condition for many fields.
     *
     * @param string $logic can be: and, or, nor
     * @return $this
     */
    public function setFieldsJoinLogic($logic = 'and')
    {
        if ($logic) {
            $logic = ltrim($logic, '$');
            $this->fieldsJoinLogic = '$' . $logic;
        }
        return $this;
    }

    public function get($logic = null)
    {
        $this->setFieldsJoinLogic($logic);
        return [$this->fieldsJoinLogic => array_map((function ($value) {
            if ($value instanceof Query) {
                return $value->get();
            }
            return $value;
        }), $this->fields)];
    }

    public function setNextCallBack(callable $function)
    {
        $this->nextCallback = $function;
        return $this;
    }

    protected function executeNextCallBack($data)
    {
        if ($this->nextCallback) {
            $data = call_user_func_array($this->nextCallback, [$data]);
            $this->nextCallback = null;
        }

        return $data;
    }

}