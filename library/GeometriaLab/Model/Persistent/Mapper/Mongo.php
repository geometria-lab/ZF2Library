<?php

namespace GeometriaLab\Model\Persistent\Mapper;

use GeometriaLab\Model\ModelInterface;

class Mongo implements MapperInterface
{
    /**
     * @var string
     */
    protected $collectionName;

    /**
     * @var string
     */
    protected $mongoInstanceName;

    /**
     * Set collection name
     *
     * @param $collectionName
     * @return Mongo
     */
    public function setCollectionName($collectionName)
    {
        $this->collectionName = $collectionName;

        return $this;
    }

    /**
     * Get collection name
     *
     * @return string
     */
    public function getCollectionName()
    {
        return $this->collectionName;
    }

    /**
     * Set mongo instance name
     *
     * @param string $instanceName
     * @return Mongo
     */
    public function setMongoInstanceName($instanceName)
    {
        $this->mongoInstanceName = $instanceName;

        return $this;
    }

    /**
     * Get mongo instance name
     *
     * @return string
     */
    public function getMongoInstanceName()
    {
        return $this->mongoInstanceName;
    }




    public function create(ModelInterface $model)
    {
        $data = $model->toArray();
        $data = $this->_prepare($data);
        $data = $this->translateToStorage($data);

    if ($this->_useRedisIds && empty($data[$this->_primaryKeyName])) {
        $data[$this->_primaryKeyName] = $this->getNextId();
    } else if ($this->_useMongoIds) {
        unset($data[$this->_primaryKeyName]);
    }

    $data = $this->_prepareInsertObject($data, $model);

    $result = $this->_getCollection()->insert($data, array('safe' => true));

    if ($result) {
        $this->_refreshModel($model, $data);
        $model->afterCreate();
        $model->afterSave();
        return true;
    } else {
        return false;
    }
}


    public function update(ModelInterface $model)
    {
        $model->beforeSave();
        $model->beforeUpdate();

        $data = $model->toArray();
        $data = $this->_prepare($data);

        if (isset($data[$this->getPrimaryKeyName()])) {
            unset($data[$this->getPrimaryKeyName()]);
        }

        $data = $this->translateToStorage($data);

        $cond = $this->_makeQuery(array($this->_primaryKeyName => $model->getPrimaryKeyValue()));
        $updateObj = $this->_prepareUpdateObj($data);
        $this->_getCollection()->update($cond['cond'], $updateObj);

        $model->afterUpdate();
        $model->afterSave();
        $model->setClean();

        return $model;
    }

    public function delete(ModelInterface $model)
    {

    }











    /**
     *
     * @return MongoCollection
     */
    protected function _getCollection()
    {
        return $this->getDb()->selectCollection($this->_collectionName);
    }

    /**
     * @return MongoDb
     */
    protected function _getDb()
    {
        return Geometria_Manager_Mongo::staticGetMongoDb($this->_mongoInstanceName);
    }

    /**
     *
     * @return Mongo
     */
    protected function _getMongo()
    {
        return Geometria_Manager_Mongo::staticGetMongo($this->_mongoInstanceName);
    }




}
