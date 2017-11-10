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

use AndyDune\MongoQuery\Operator\{
    Between, Equal, In, Not, GreaterThan, LessThan, NotEqual,
    Limit, Skip, SortAsc, SortDesc
};

/**
 * @method $this between($value1, $value2)
 * @method $this in(...$values)
 * @method $this gt($value)
 * @method $this eq($value)
 * @method $this lt($value)
 * @method $this limit($value)
 * @method $this skip($value)
 * @method $this offset($value)
 * @method $this sortAsc()
 * @method $this sortDesc()
 *
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

    /**
     * Options for mongo requests within find and findOne methods.
     *
     * @var array
     */
    protected $findOptions = [];

    protected $operators = [
        'in' => In::class,
        'between' => Between::class,
        'gt' => GreaterThan::class,
        'lt' => LessThan::class,
        'eq' => Equal::class,
        'ne' => NotEqual::class,
        'limit' => Limit::class,
        'skip'  => Skip::class,
        'offset'   => Skip::class, // alias for skip
        'sortAsc'  => SortAsc::class,
        'sortDesc' => SortDesc::class
    ];

    protected $fieldsJoinLogic = '$and';

    public function __construct($fieldsMap = null)
    {
        $this->fieldsCorrector = new FieldTypeCorrector($fieldsMap);
    }

    public function mergeFindOptions($options)
    {
        $this->findOptions = array_replace($this->findOptions, $options);
        return $this;
    }

    public function getFindOptions()
    {
        return $this->findOptions;
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
        if (is_callable($function)) {
            $result = $function(...$arguments);
            if ($result) {
                $this->fields[] = $this->executeNextCallBack($result);
            }
        }

        return $this;
    }

    /**
     * Select records with accumulated conditions.
     *
     * @param \MongoDB\Collection $collection
     * @param array $options
     * @return \MongoDB\Driver\Cursor
     */
    public function find(\MongoDB\Collection $collection, $options = [])
    {
        return $collection->find($this->get(), array_replace($this->getFindOptions(), $options));
    }

    /**
     * Select one record with accumulated conditions.
     *
     * @param \MongoDB\Collection $collection
     * @param array $options
     * @return array|null|object
     */
    public function findOne(\MongoDB\Collection $collection, $options = [])
    {
        return $collection->findOne($this->get(), array_replace($this->getFindOptions(), $options));
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
        if (!$this->fields) {
            return [];
        }
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