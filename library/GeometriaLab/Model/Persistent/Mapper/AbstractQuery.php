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
     * @param $mapper
     * @return AbstractQuery
     */
    public function setMapper($mapper)
    {
        $this->mapper = $mapper;

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
     * @return AbstractQuery
     */
    public function resetWhere()
    {
        $this->where = null;

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
     * @return AbstractQuery
     */
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

    public function hasLimit()
    {
        return $this->limit !== null;
    }

    public function resetLimit()
    {
        $this->limit = null;

        return $this;
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

    public function hasOffset()
    {
        return $this->offset !== null;
    }

    public function resetOffset()
    {
        $this->offset = null;

        return $this;
    }
}
