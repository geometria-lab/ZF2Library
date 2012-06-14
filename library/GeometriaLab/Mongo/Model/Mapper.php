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
     * Property names map
     *
     * @var array
     */
    protected $propertyNamesMap = array(
        'id' => '_id'
    );

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
        return $this->getByCondition(array('_id' => $id));
    }

    /**
     * Get model by condition
     *
     * @param array $condition
     * @return ModelInterface
     */
    public function getByCondition(array $condition)
    {
        $query = $this->query()->condition($condition);

        return $this->getAllByQuery($query)->getFirst();
    }

    /**
     * Get models collection by query
     *
     * @param Query $query
     * @return CollectionInterface
     */
    public function getAllByQuery(Query $query)
    {
        $cursor = $this->find($query);

        $collection = new $this->collectionClass($cursor);

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
        $cursor = $this->getMongoCollection()->find($query->getCondition(), $query->getFields());

        if ($query->getSort() !== null) {
            $cursor->sort($query->getSort());
        }

        if ($query->getLimit() !== null) {
            $cursor->limit($query->getLimit());
        }

        if ($query->getOffset() !== null) {
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
     */
    public function create(ModelInterface $model)
    {
        $this->validateModel($model);

        $modelData = $model->toArray();

        if (!isset($modelData['id']) && $this->getPrimaryKeyGenerator()) {
            $modelData['id'] = new \MongoId($this->getPrimaryKeyGenerator()->generate());
        }

        $storageData = $this->transformModelDataForStorage($modelData);

        $result = $this->getMongoCollection()->insert($storageData, array('safe' => true));

        if ($result) {
            $model->set('id', (string)$storageData['_id']);
            $model->markClean();

            return true;
        } else {
            return false;
        }
    }

    public function update(PersistentInterface $model)
    {
        $this->validateModel($model);

        $modelData = $model->toArray();

        $storageData = $this->transformModelDataForStorage($modelData);

        $criteria = array('_id' => new MongoId($storageData['_id']));

        $this->getMongoCollection()->update($criteria, array('$set' => $storageData));

        $model->markClean();

        return true;
    }

    public function updateByCondition($condition, $data)
    {
        return $this->_getCollection()->update($query['cond'], array('$set' => $data), array('multiple' => true));
    }

    public function delete(PersistentInterface $model)
    {
        $propertyNamesMap = array_flip($this->propertyNamesMap);
        $keyName = isset($propertyNamesMap['_id']) ? $propertyNamesMap['_id'] : '_id';

        $criteria = array(
            '_id' => $model->get($keyName)
        );

        $result = $this->getMongoCollection()->remove($criteria, array('safe' => true));

        if ($result) {
            $model->markClean(false);

            return true;
        } else {
            return false;
        }
    }

    public function deleteByCondition($condition)
    {

    }

    /**
     * Get query
     *
     * @return Query
     */
    public function query()
    {
        return new Query($this);
    }

    protected function validateModel(PersistentInterface $model)
    {
        parent::validateModel($model);

        if (!$this->isPrimaryKeysValidated) {
            $primaryPropertiesNames = $model->getDefinition()->getPrimaryPropertyNames();

            if (count($primaryPropertiesNames) > 1) {
                throw new \InvalidArgumentException('Mongo mapper supports only one primary property');
            }

            if ($primaryPropertiesNames[0] !== 'id') {
                throw new \InvalidArgumentException("Primary property must be 'id'");
            }

            if (!isset($this->propertyNamesMap['id']) || $this->propertyNamesMap['id'] !== '_id') {
                throw new \InvalidArgumentException("Primary property 'id' must be mapped to mongo default '_id' field");
            }
        }

        $this->isPrimaryKeysValidated = true;
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
