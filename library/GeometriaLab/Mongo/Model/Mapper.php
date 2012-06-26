<?php

namespace GeometriaLab\Mongo\Model;

use GeometriaLab\Mongo,
    GeometriaLab\Model\Persistent\ModelInterface,
    GeometriaLab\Model\Persistent\CollectionInterface,
    GeometriaLab\Model\Persistent\Mapper\AbstractMapper,
    GeometriaLab\Model\Persistent\Mapper\QueryInterface,
    GeometriaLab\Model\Persistent\Schema\Property\ArrayProperty,
    GeometriaLab\Model\Persistent\Schema\Property\ModelProperty;

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
     * @param QueryInterface $query
     * @return CollectionInterface
     */
    public function getAll(QueryInterface $query = null)
    {
        if ($query === null) {
            $query = $this->createQuery();
        }

        $cursor = $this->find($query);

        $modelClass = $this->getModelClass();
        $collectionClass = $this->getCollectionClass();

        /**
         * @var CollectionInterface $collection
         */
        $collection = new $collectionClass();

        foreach($cursor as $document) {
            /**
             * @var ModelInterface $model
             */
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
        $arguments = array();

        if ($query->hasWhere()) {
            $arguments[] = $this->transformModelDataForStorage($query->getWhere());
        }
        if ($query->hasSelect()) {
            $arguments[] = $this->transformModelDataForStorage($query->getSelect());
        }

        /**
         * @var \MongoCursor $cursor
         */
        $cursor = call_user_func_array(array($this->getMongoCollection(), 'find'), $arguments);

        if ($query->hasSort()) {
            $sort = $this->transformModelDataForStorage($query->getSort());

            foreach($sort as $field => &$ascending) {
                $ascending = $ascending ? 1 : -1;
            }

            $cursor->sort($sort);
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
            $query = $this->createQuery()->where($condition);
            $condition = $this->transformModelDataForStorage($query->getWhere());
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
        $data = $this->getModelDataForStorage($model);

        if (!isset($data['_id']) && $this->getPrimaryKeyGenerator()) {
            $data['_id'] = new \MongoId($this->getPrimaryKeyGenerator()->generate());
        }

        $result = $this->getMongoCollection()->insert($data, array('safe' => true));

        if ($result) {
            $model->set('id', $data['_id']);
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
        $data = $this->getModelDataForStorage($model, true);

        if ($data['_id']) {
            $id = $model->getClean('id');
        } else {
            $id = $model->get('id');
        }

        if ($id === null) {
            throw new \InvalidArgumentException('Cant update model - primary property id is empty');
        }

        $this->getMongoCollection()->update(array('_id' => new \MongoId($id)), array('$set' => $data));

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
        $condition = $this->transformModelDataForStorage($query->getWhere());

        $storageData = $this->transformModelDataForStorage($data);

        return $this->getMongoCollection()->update($condition, array('$set' => $storageData), array('multiple' => true));
    }

    /**
     * Delete model
     *
     * @param ModelInterface $model
     * @return boolean
     */
    public function delete(ModelInterface $model)
    {
        $id = $model->get('id');

        if ($id === null) {
            throw new \InvalidArgumentException('Cant delete model - primary property id is empty');
        }

        $condition = array('_id' => new \MongoId($id));

        $result = $this->getMongoCollection()->remove($condition, array('safe' => true));

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
        $condition = $this->transformModelDataForStorage($query->getWhere());

        return $this->getMongoCollection()->remove($condition);
    }

    /**
     * Create query object
     *
     * @return \GeometriaLab\Model\Persistent\Mapper\QueryInterface|Query
     */
    public function createQuery()
    {
        return new Query($this);
    }

    /**
     * Get and validate model data for storage
     *
     * @param ModelInterface $model
     * @param boolean $changed
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function getModelDataForStorage(ModelInterface $model, $changed = false)
    {
        if (!is_a($model, $this->getModelClass())) {
            throw new \InvalidArgumentException("Model must be {$this->getModelClass()}");
        }

        $data = array();
        $primary = array();

        /**
         * @var \GeometriaLab\Model\Persistent\Schema\Property\PropertyInterface $property
         */
        foreach($model->getSchema()->getProperties() as $name => $property) {
            if ($property->isPersistent()) {
                if (!$changed || $model->isPropertyChanged($name)) {
                    $data[$name] = $model->get($name);

                    if ($property instanceof ModelProperty) {
                        $data[$name] = $data[$name]->toArray(-1);
                    } else if ($property instanceof ArrayProperty && $property->getItemProperty() instanceof ModelProperty) {
                        foreach($data[$name] as &$item) {
                            $item = $item->toArray(-1);
                        }
                    }
                }
                if ($property->isPrimary()) {
                    $primary[] = $name;
                }
            }
        }

        if (count($primary) !== 1 || $primary[0] !== 'id') {
            throw new \InvalidArgumentException("Mongo mapper support only one primary key 'id'");
        }

        return $this->transformModelDataForStorage($data);
    }

    protected function transformModelDataForStorage(array $data)
    {
        $data = parent::transformModelDataForStorage($data);

        if (isset($data['id'])) {
            $data['_id'] = new \MongoId($data['id']);
            unset($data['id']);
        }

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
