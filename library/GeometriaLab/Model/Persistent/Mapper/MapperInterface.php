<?php

namespace GeometriaLab\Model\Persistent\Mapper;

use GeometriaLab\Model\Persistent\ModelInterface,
    GeometriaLab\Model\Persistent\CollectionInterface;

interface MapperInterface
{
    /**
     * Get model by primary key
     *
     * @param integer $id
     * @return ModelInterface
     */
    public function get($id);

    /**
     * Get model by condition
     *
     * @param array $condition
     * @return ModelInterface
     */
    public function getByCondition(array $condition);

    /**
     * Get models collection by query
     *
     * @param QueryInterface $query
     * @return CollectionInterface
     */
    public function getAll(QueryInterface $query = null);

    /**
     * @abstract
     * @param array $condition
     * @return integer
     */
    public function count(array $condition = array());

    /**
     * @abstract
     * @param ModelInterface $model
     * @return boolean
     */
    public function create(ModelInterface $model);

    /**
     * @abstract
     * @param ModelInterface $model
     * @return boolean
     */
    public function update(ModelInterface $model);

    /**
     * @abstract
     * @param array $data
     * @param array $condition
     * @return boolean
     */
    public function updateByCondition(array $data, array $condition);

    /**
     * @abstract
     * @param ModelInterface $model
     * @return boolean
     */
    public function delete(ModelInterface $model);

    /**
     * @abstract
     * @param array $condition
     * @return boolean
     */
    public function deleteByCondition(array $condition);

    /**
     * @abstract
     * @return QueryInterface
     */
    public function createQuery();

    /**
     * @abstract
     * @return string
     */
    public function getModelClass();

    /**
     * @abstract
     * @param string $modelClass
     * @return MapperInterface
     */
    public function setModelClass($modelClass);
}