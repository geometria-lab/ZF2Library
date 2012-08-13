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
     * Get model by query
     *
     * @param QueryInterface $query
     * @return ModelInterface
     */
    public function getOne(QueryInterface $query = null);

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
     * @param ModelInterface $model
     * @return boolean
     */
    public function delete(ModelInterface $model);

    /**
     * @abstract
     * @param QueryInterface $query
     * @return mixed
     */
    public function deleteByQuery(QueryInterface $query);

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