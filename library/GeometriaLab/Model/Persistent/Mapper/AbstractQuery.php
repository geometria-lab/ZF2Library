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
        $this->mapper = $mapper;
    }

    /**
     * Select fields
     *
     * @param array $fields
     * @return AbstractQuery|QueryInterface
     */
    public function select(array $fields)
    {
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
     * @return AbstractQuery|QueryInterface
     */
    public function resetSelect()
    {
        $this->select = null;

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

    public function resetWhere()
    {
        $this->where = null;

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
