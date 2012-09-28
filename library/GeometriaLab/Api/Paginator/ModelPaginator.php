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
     * @var Collection
     */
    protected $collection;

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
     * Get items by limit and offset
     *
     * @param integer $limit
     * @param integer $offset
     * @return Collection
     */
    public function getItems($limit, $offset)
    {
        $this->getQuery()->limit($limit);
        $this->getQuery()->offset($offset);

        $mapper = $this->getQuery()->getMapper();

        return $mapper->getAll($this->getQuery());
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
     */
    public function setQuery(QueryInterface $query)
    {
        $this->query = $query;
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
