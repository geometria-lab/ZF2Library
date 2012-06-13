<?php

namespace GeometriaLab\Mongo\Model;

use GeometriaLab\Mongo,
    GeometriaLab\Model\Persistent\ModelInterface,
    GeometriaLab\Model\Persistent\Collection,
    GeometriaLab\Model\Persistent\Mapper\AbstractMapper;

class Mapper extends AbstractMapper
{
    /**
     * @abstract
     * @param $id
     * @return PersistentInterface
     */
    public function get($id)
    {
        return $this->getByQuery($this->query()->condition(array('_id' => $id)));
    }

    /**
     *
     * @param Query $query
     * @return PersistentInterface
     */
    public function getByQuery(Query $query)
    {
        $this->fetch()
    }

    /**
     * @abstract
     * @param Query $query
     * @return CollectionInterface
     */
    public function getAllByQuery(Query $query)
    {
        $cursor = $this->find($query);
    }

    /**
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
     * Get query
     *
     * @return Query
     */
    public function query()
    {
        return new Query($this);
    }










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
     *
     * @param  string $id
     * @return Geometria_Model_Interface
     */
    public function fetch($id)
    {
        $key = $this->_translateKey($this->_primaryKeyName, false);
        return $this->fetchOne(array($key => $id));
    }

    /**
     *
     * @param array   $cond
     * @param array   $sort
     * @param integer $count
     * @param integer $offset
     *
     * @return Geometria_Model_Collection
     */
    public function fetchAll(array $cond = null, array $sort = null, $count = null, $offset = null)
    {
        $query = $this->_makeQuery($cond, $sort, $count, $offset);
        return $this->findByQuery($query);
    }

    /**
     *
     * @param MongoCursor $cursor
     * @param string $valueField
     * @param string $keyField
     * @return array
     */
    protected function _collectFieldFromCursor($cursor, $valueField, $keyField = null)
    {
        $ids = array();

        foreach ($cursor as $item) {
            if (null !== $keyField) {
                $ids [$item[$keyField]]= $item[$valueField];
            } else {
                $ids []= $item[$valueField];
            }
        }

        return $ids;
    }

    /**
     *
     * @param array $cond
     * @return integer
     */
    public function getCount(array $condition = null)
    {
        $query = $this->_makeQuery($cond);
        return $this->countByQuery($query);
    }

    /**
     *
     * @param array $query
     * @return integer
     */
    public function countByQuery($query)
    {
        return $this->_getCollection()->count($query['cond']);
    }

    /**
     *
     * @param array $cond
     * @param array $sort
     *
     * @return Geometria_Model_Interface
     */
    public function fetchOne(array $cond = null, array $sort = null)
    {
        $query = $this->_makeQuery($cond, $sort, 1, 0);
        $data = $this->findByQuery($query);
        return $data->current();
    }

    /**
     *
     * @param array $query
     * @return Geometria_Model_Collection
     */
    protected function findByQuery($query)
    {
        if (empty($query['fields'])) {
            $cursor = $this->_getCollection()->find($query['cond']);
        } else {
            $cursor = $this->_getCollection()->find($query['cond'], $query['fields']);
        }

        if (!empty($query['sort'])) {
            $cursor->sort($query['sort']);
        }

        if (!empty($query['limit'])) {
            $cursor->limit($query['limit']);
        }

        if (!empty($query['offset'])) {
            $cursor->skip($query['offset']);
        }

        $data = iterator_to_array($cursor);

        $cursor->reset();

        return $this->createModelCollection($data);
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
