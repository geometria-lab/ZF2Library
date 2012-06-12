<?php

namespace GeometriaLab\Model\Persistent\Mapper;

use GeometriaLab\Model\Persistent\Mapper\MapperInterface;

abstract class AbstractQuery implements QueryInterface
{
    /**
     * Mapper
     *
     * @var MapperInterface
     */
    protected $mapper;

    /**
     * Fields
     *
     * @var array|null
     */
    protected $fields;

    /**
     * Condition
     *
     * @var array|null
     */
    protected $condition;

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
        $this->mapper = $mapper;
    }

    /**
     * @param array $fields
     * @return AbstractQuery|QueryInterface
     */
    public function fields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return AbstractQuery|QueryInterface
     */
    public function resetFields()
    {
        $this->fields = null;

        return $this;
    }

    /**
     * Get conditions
     *
     * @return array|null
     */
    public function getCondition()
    {
        return $this->condition;
    }

    public function resetCondition()
    {
        $this->condition = null;

        return $this;
    }

    public function getSort()
    {
        return $this->sort;
    }

    public function resetSort()
    {
        $this->sort = null;

        return $this;
    }

    public function limit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function offset($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    public function getOffset()
    {
        return $this->offset;
    }
}
