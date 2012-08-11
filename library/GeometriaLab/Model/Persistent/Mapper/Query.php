<?php

namespace GeometriaLab\Model\Persistent\Mapper;

use GeometriaLab\Model\Persistent\Mapper\MapperInterface,
    GeometriaLab\Model\Schema\Schema,
    GeometriaLab\Model\Schema\Manager as SchemaManager;

class Query implements QueryInterface
{
    /**
     * Mapper
     *
     * @var MapperInterface
     */
    protected $mapper;

    /**
     * Select
     *
     * @var array|null
     */
    protected $select;

    /**
     * Where
     *
     * @var array|null
     */
    protected $where;

    /**
     * Sort
     *
     * @var array|null
     */
    protected $sort;

    /**
     * Limit
     *
     * @var integer|null
     */
    protected $limit;

    /**
     * Offset
     *
     * @var integer|null
     */
    protected $offset;

    /**
     * Constructor
     *
     * @param MapperInterface $mapper
     */
    public function __construct(MapperInterface $mapper)
    {
        $this->setMapper($mapper);
    }

    /**
     * Get mapper
     *
     * @return MapperInterface
     */
    public function getMapper()
    {
        return $this->mapper;
    }

    /**
     * Set mapper
     *
     * @param MapperInterface $mapper
     * @return Query
     */
    public function setMapper(MapperInterface $mapper)
    {
        $this->mapper = $mapper;

        return $this;
    }

    /**
     * Set selected fields
     *
     * @param array $fields
     * @return QueryInterface|Query
     * @throws \InvalidArgumentException
     */
    public function select(array $fields)
    {
        foreach($fields as $field) {
            if (!$this->getModelSchema()->hasProperty($field)) {
                throw new \InvalidArgumentException("Selected field '$field' not present in model!");
            }
        }

        $this->select = $fields;

        return $this;
    }

    /**
     * Get select
     *
     * @return array|null
     */
    public function getSelect()
    {
        return $this->select;
    }

    /**
     * Has select?
     *
     * @return boolean
     */
    public function hasSelect()
    {
        return $this->select !== null;
    }

    /**
     * Reset select
     *
     * @return Query|QueryInterface
     */
    public function resetSelect()
    {
        $this->select = null;

        return $this;
    }

    /**
     * Add where condition
     *
     * @param array $where
     * @return QueryInterface|Query
     */
    public function where(array $where)
    {
        if (!empty($where)) {
            $conditions = array();
            foreach($where as $field => $value) {
                $conditions[$field] = $this->prepareFieldValue($field, $value);
            }

            if ($this->where === null) {
                $this->where = $conditions;
            } else {
                $this->where = array_merge($this->where, $conditions);
            }
        }

        return $this;
    }

    /**
     * Get where
     *
     * @return array|null
     */
    public function getWhere()
    {
        return $this->where;
    }

    /**
     * Has where?
     *
     * @return boolean
     */
    public function hasWhere()
    {
        return $this->where !== null;
    }

    /**
     * Reset where
     *
     * @return Query|QueryInterface
     */
    public function resetWhere()
    {
        $this->where = null;

        return $this;
    }

    /**
     * Add sorting by field
     *
     * @param string $field
     * @param boolean $ascending
     * @return Query|QueryInterface
     * @throws \InvalidArgumentException
     */
    public function sort($field, $ascending = true)
    {
        if (!$this->getModelSchema()->hasProperty($field)) {
            throw new \InvalidArgumentException("Sorted field '$field' not present in model!");
        }

        $sort = array($field => $ascending);

        if ($this->sort === null) {
            $this->sort = $sort;
        } else {
            $this->sort = array_merge($this->sort, $sort);
        }

        return $this;
    }

    /**
     * Get sort
     *
     * @return array|null
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Has sort?
     *
     * @return bool
     */
    public function hasSort()
    {
        return $this->sort !== null;
    }

    /**
     * Reset sort
     *
     * @return Query
     */
    public function resetSort()
    {
        $this->sort = null;

        return $this;
    }

    /**
     * Set limit
     *
     * @param integer $limit
     * @return Query
     */
    public function limit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Get limit
     *
     * @return int|null
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Has limit?
     *
     * @return bool
     */
    public function hasLimit()
    {
        return $this->limit !== null;
    }

    /**
     * Reset limit
     *
     * @return Query
     */
    public function resetLimit()
    {
        $this->limit = null;

        return $this;
    }

    /**
     * Set offset
     *
     * @param integer $offset
     * @return Query
     */
    public function offset($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * Get offset
     *
     * @return int|null
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Has offset?
     *
     * @return bool
     */
    public function hasOffset()
    {
        return $this->offset !== null;
    }

    /**
     * Reset offset
     *
     * @return Query
     */
    public function resetOffset()
    {
        $this->offset = null;

        return $this;
    }

    /**
     * Prepare field value
     *
     * @param string $field
     * @param mixed $value
     * @return mixed
     * @throws \InvalidArgumentException
     */
    protected function prepareFieldValue($field, $value)
    {
        if (!$this->getModelSchema()->hasProperty($field)) {
            throw new \InvalidArgumentException("Field in where '$field' not present in model!");
        }

        return $this->getModelSchema()->getProperty($field)->prepare($value);
    }

    /**
     * Get model schema
     *
     * @return Schema
     */
    protected function getModelSchema()
    {
        $modelClass = $this->getMapper()->getModelClass();

        return $modelClass::getSchema();
    }
}
