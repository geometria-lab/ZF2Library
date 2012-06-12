<?php

namespace GeometriaLab\Model\Persistent\Mapper;

interface QueryInterface
{
    /**
     * Returned fields
     *
     * @param array $fields
     * @return QueryInterface
     */
    public function fields(array $fields);

    /**
     * Reset returned fields
     *
     * @return QueryInterface
     */
    public function resetFields();

    /**
     * Add condition
     *
     * @param array $condition
     * @return QueryInterface
     */
    public function condition(array $condition);

    public function getCondition();

    public function resetCondition();

    public function sort($field, $ascending = true);

    public function getSort();

    public function resetSort();

    public function limit($limit);

    public function getLimit();

    public function offset($offset);

    public function getOffset();
}
