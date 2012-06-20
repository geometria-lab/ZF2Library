<?php

namespace GeometriaLab\Model\Persistent\Models;

use GeometriaLab\Model\Persistent\Mapper\AbstractMapper;


class MockMapper extends AbstractMapper
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @param array $condition
     * @return integer
     */
    public function count(array $condition = array())
    {
        if (!empty($condition)) {
            $condition = $this->createQuery()->where($condition)->getWhere();
        }

        return $this->getMongoCollection()->count($condition);
    }

    /**
     * Get model by primary key
     *
     * @param integer $id
     * @return ModelInterface
     */
    public function get($id)
    {
        // TODO: Implement get() method.
    }

    /**
     * Get model by condition
     *
     * @param array $condition
     * @return ModelInterface
     */
    public function getByCondition(array $condition)
    {
        // TODO: Implement getByCondition() method.
    }

    /**
     * Get models collection by query
     *
     * @param QueryInterface $query
     * @return CollectionInterface
     */
    public function getAll(QueryInterface $query = null)
    {
        // TODO: Implement getAll() method.
    }

    /**
     * @param ModelInterface $model
     * @return boolean
     */
    public function create(ModelInterface $model)
    {
        // TODO: Implement create() method.
    }

    /**
     * @param ModelInterface $model
     * @return boolean
     */
    public function update(ModelInterface $model)
    {
        // TODO: Implement update() method.
    }

    /**
     * @param array $data
     * @param array $condition
     * @return boolean
     */
    public function updateByCondition(array $data, array $condition)
    {
        // TODO: Implement updateByCondition() method.
    }

    /**
     * @param ModelInterface $model
     * @return boolean
     */
    public function delete(ModelInterface $model)
    {
        // TODO: Implement delete() method.
    }

    /**
     * @param array $condition
     * @return boolean
     */
    public function deleteByCondition(array $condition)
    {
        // TODO: Implement deleteByCondition() method.
    }

    /**
     * @return QueryInterface
     */
    public function createQuery()
    {
        // TODO: Implement createQuery() method.
    }

}
