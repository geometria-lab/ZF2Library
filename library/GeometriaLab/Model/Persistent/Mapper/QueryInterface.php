<?php

namespace GeometriaLab\Model\Persistent\Mapper;

interface QueryInterface
{
    /**
     * Selected fields
     *
     * @param array $fields
     * @return QueryInterface
     */
    public function select(array $fields);

    /**
     * @abstract
     * @return array|null
     */
    public function getSelect();

    /**
     * @abstract
     * @return boolean
     */
    public function hasSelect();

    /**
     * Reset returned fields
     *
     * @return QueryInterface
     */
    public function resetSelect();

    /**
     * Add condition
     *
     * @param array $condition
     * @return QueryInterface
     */
    public function where(array $condition);

    /**
     * @abstract
     * @return array|null
     */
    public function getWhere();

    /**
     * @abstract
     * @return boolean
     */
    public function hasWhere();

    /**
     * @abstract
     * @return QueryInterface
     */
    public function resetWhere();

    /**
     * @abstract
     * @param $field
     * @param bool $ascending
     * @return QueryInterface
     */
    public function sort($field, $ascending = true);

    public function getSort();

    public function hasSort();

    public function resetSort();

    public function limit($limit);

    public function getLimit();

    public function hasLimit();

    public function resetLimit();

    public function offset($offset);

    public function getOffset();

    public function hasOffset();

    public function resetOffset();

    /**
     * Get mapper
     *
     * @return MapperInterface
     */
    public function getMapper();

    /**
     * Set mapper
     *
     * @param MapperInterface $mapper
     * @return QueryInterface
     */
    public function setMapper(MapperInterface $mapper);
}