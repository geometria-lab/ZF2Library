<?php

namespace GeometriaLab\Mongo\Model;

use GeometriaLab\Mongo,
    GeometriaLab\Model\Persistent\ModelInterface,
    GeometriaLab\Model\Persistent\CollectionInterface,
    GeometriaLab\Model\Persistent\Mapper\AbstractMapper;

class Mapper extends AbstractMapper
{
    /**
     * Mongo instance name
     *
     * @var string
     */
    protected $mongoInstanceName;

    /**
     * Mongo collection name
     *
     * @var string
     */
    protected $collectionName;

    /**
     * Flag for validate model primary keys only once
     *
     * @var bool
     */
    protected $isPrimaryKeysValidated = false;

    /**
     * Set collection name
     *
     * @param $collectionName
     * @return Mapper
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
     * @param string $mongoInstanceName
     * @return Mapper
     */
    public function setMongoInstanceName($mongoInstanceName)
    {
        $this->mongoInstanceName = $mongoInstanceName;

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

    /**
     * Get model by primary key
     *
     * @param integer $id
     * @return ModelInterface
     */
    public function get($id)
    {
        return $this->getByCondition(array('id' => $id));
    }

    /**
     * Get model by condition
     *
     * @param array $condition
     * @return ModelInterface
     */
    public function getByCondition(array $condition)
    {
        $query = $this->createQuery()->where($condition)->limit(1);

        return $this->getAll($query)->getFirst();
    }

    /**
     * Get models collection by query
     *
     * @param Query $query
     * @return CollectionInterface
     */
    public function getAll(Query $query = null)
    {
        if ($query === null) {
            $query = $this->createQuery();
        }

        $cursor = $this->find($query);

        $modelClass = $this->getModelClass();
        $collectionClass = $this->getCollectionClass();

        $collection = new $collectionClass();

        foreach($cursor as $document) {
            $model = new $modelClass($document);
            $model->populate($document);

            $collection->push($model);
        }

        $cursor->reset();

        return $collection;
    }

    /**
     * Find monogo documents by query
     *
     * @param Query $query
     * @return \MongoCursor
     */
    protected function find(Query $query)
    {
        $cursor = $this->getMongoCollection()->find($query->getWhere(), $query->getSelect());

        if ($query->hasSort()) {
            $cursor->sort($query->getSort());
        }

        if ($query->hasLimit()) {
            $cursor->limit($query->getLimit());
        }

        if ($query->hasOffset()) {
            $cursor->skip($query->getOffset());
        }

        return $cursor;
    }

    /**
     * Count
     *
     * @param array $condition
     * @return integer
     */
    public function count(array $condition = array())
    {
        if (!empty($condition)) {
            $condition = $this->query()->condition($condition)
                                       ->getCondition();
        }

        return $this->getMongoCollection()->count($condition);
    }

    /**
     * Create model in storage
     *
     * @param ModelInterface $model
     * @return boolean
     * @throws \InvalidArgumentException
     */
    public function create(ModelInterface $model)
    {
        if (!is_a($model, $this->getModelClass())) {
            throw new \InvalidArgumentException("Model must be {$this->getModelClass()}");
        }

        $data = array();
        $primary = array();
        foreach($model->getSchema()->getProperties() as $name => $property) {
            if ($property->isPersistent()) {
                $data[$name] = $model->get($name);
                if ($property->isPrimary()) {
                    $primary[] = $name;
                }
            }
        }

        if (count($primary) !== 1 || $primary[0] !== 'id') {
            throw new \InvalidArgumentException("Mongo mapper support only one primary key 'id'");
        }

        // After this line does not work

        if (!isset($data['id']) && $this->getPrimaryKeyGenerator()) {
            $data['_id'] = $this->getPrimaryKeyGenerator()->generate();
        }

        $storageData = $this->transformModelDataForStorage($data);

        $result = $this->getMongoCollection()->insert($storageData, array('safe' => true));

        if ($result) {
            $model->set('id', $storageData['_id']);
            $model->markClean();

            return true;
        } else {
            return false;
        }
    }

    /**
     * Update model
     *
     * @param ModelInterface $model
     * @return bool
     */
    public function update(ModelInterface $model)
    {
        $this->validateModel($model);

        $modelData = $model->toArray();

        $query = $this->createQuery()->where(array('id' => $modelData['id']));

        $storageData = $this->transformModelDataForStorage($modelData);

        unset($storageData['_id']);

        $this->getMongoCollection()->update($query->getWhere(), array('$set' => $storageData));

        $model->markClean();

        return true;
    }

    /**
     * Update by condition
     *
     * @param array $data
     * @param array $condition
     * @return boolean
     */
    public function updateByCondition(array $data, array $condition)
    {
        $query = $this->createQuery()->where($condition);

        $storageData = $this->transformModelDataForStorage($data);

        return $this->getMongoCollection()->update($query->getWhere(), array('$set' => $storageData), array('multiple' => true));
    }

    /**
     * Delete model
     *
     * @param ModelInterface $model
     * @return boolean
     */
    public function delete(ModelInterface $model)
    {
        $query = $this->createQuery()->where(array('id' => $model->get('id')));

        $result = $this->getMongoCollection()->remove($query->getWhere(), array('safe' => true));

        if ($result) {
            $model->markClean(false);

            return true;
        } else {
            return false;
        }
    }

    /**
     * Delete by condition
     *
     * @param array $condition
     * @return boolean|mixed
     */
    public function deleteByCondition(array $condition)
    {
        $query = $this->createQuery()->where($condition);

        return $this->getMongoCollection()->remove($query->getWhere());
    }

    /**
     * Create query
     *
     * @return Query
     */
    public function createQuery()
    {
        return new Query($this);
    }

    public function transformModelDataForStorage(array $data)
    {
        $data = parent::transformModelDataForStorage($data);

        if (isset($data['_id'])) {
            $data['_id'] = new MongoId($data['_id']);
        }

        return $data;
    }

    public function transformStorageDataForModel(array $data)
    {
        $data = parent::transformStorageDataForModel($data);

        return $data;
    }

    /**
     * Get MongoDB instance
     *
     * @return \MongoDB
     */
    protected function getMongo()
    {
        return Mongo\Manager::getInstance()->get($this->getMongoInstanceName());
    }

    /**
     * Get MongoCollection
     *
     * @return \MongoCollection
     */
    protected function getMongoCollection()
    {
        return $this->getMongo()->selectCollection($this->getCollectionName());
    }
}
