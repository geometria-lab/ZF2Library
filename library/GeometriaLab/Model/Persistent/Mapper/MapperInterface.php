<?php

namespace GeometriaLab\Model\Persistent\Mapper;

use GeometriaLab\Model\Persistent\ModelInterface,
    GeometriaLab\Model\Persistent\CollectionInterface;

interface MapperInterface
{
    /**
     * @abstract
     * @param integer|array $id
     * @return ModelInterface|CollectionInterface
     */
    public function get($id);

    /**
     * @abstract
     * @param QueryInterface $query
     * @return CollectionInterface
     */
    public function getByQuery(QueryInterface $query);

    /**
     * @abstract
     * @param null $condition
     * @return integer
     */
    public function count(array $condition = array());

    /**
     * @abstract
     * @param PersistentInterface $model
     * @return boolean
     */
    public function create(PersistentInterface $model);

    /**
     * @abstract
     * @param PersistentInterface $model
     * @return boolean
     */
    public function update(PersistentInterface $model);

    /**
     * @abstract
     * @param array $data
     * @param array $condition
     * @return boolean
     */
    public function updateByCondition(array $data, array $condition = array());

    /**
     * @abstract
     * @param PersistentInterface $model
     * @return boolean
     */
    public function delete(PersistentInterface $model);

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
    public function query();
}