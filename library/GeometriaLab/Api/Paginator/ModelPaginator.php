<?php

namespace GeometriaLab\Api\Paginator;

use GeometriaLab\Model\Persistent\Collection,
    GeometriaLab\Model\Persistent\Mapper\QueryInterface;

class ModelPaginator implements \Countable
{
    /**
     * @var QueryInterface
     */
    protected $query;

    /**
     * Limit
     *
     * @var integer
     */
    protected $limit;

    /**
     * Offset
     *
     * @var integer
     */
    protected $offset;

    /**
     * Constructor
     *
     * @param QueryInterface $query
     */
    public function __construct(QueryInterface $query)
    {
        $this->setQuery($query);
    }

    /**
     * Get query
     *
     * @return QueryInterface
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set query
     *
     * @param QueryInterface $query
     * @return ModelPaginator
     */
    public function setQuery(QueryInterface $query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @param integer $limit
     * @return ModelPaginator
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @return integer
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param integer $offset
     * @return ModelPaginator
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @return integer
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Get items by limit and offset
     *
     * @return Collection
     * @throws \RuntimeException
     */
    public function getItems()
    {
        $limit = $this->getLimit();
        $offset = $this->getOffset();

        if ($limit === null) {
            throw new \RuntimeException('Limit must be positive integer');
        }

        $this->getQuery()->limit($limit)
                         ->offset($offset);

        $mapper = $this->getQuery()->getMapper();

        return $mapper->getAll($this->getQuery());
    }

    /**
     * Returns the total number of items
     *
     * @return integer
     */
    public function count()
    {
        $mapper = $this->getQuery()->getMapper();

        return $mapper->count($this->getQuery()->getWhere());
    }
}
