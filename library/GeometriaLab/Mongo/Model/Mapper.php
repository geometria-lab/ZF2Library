<?php

namespace GeometriaLab\Mongo\Model;

use GeometriaLab\Mongo,
    GeometriaLab\Model\Persistent\ModelInterface,
    GeometriaLab\Model\Persistent\CollectionInterface,
    GeometriaLab\Model\Persistent\Mapper\AbstractMapper,
    GeometriaLab\Model\Persistent\Mapper\QueryInterface,
    GeometriaLab\Model\Persistent\Schema\Property\ArrayProperty,
    GeometriaLab\Model\Persistent\Schema\Property\ModelProperty,
    GeometriaLab\Model\Persistent\Schema\Property\Relation\AbstractRelation,
    GeometriaLab\Model\Persistent\Schema\Property\Relation\AbstractHasRelation,
    GeometriaLab\Model\Persistent\Schema\Property\Relation\HasMany;

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
        $query = $this->createQuery()->where(array('id' => $id));

        return $this->getOne($query);
    }

    /**
     * Get model by query
     *
     * @param QueryInterface $query
     * @return ModelInterface
     * @throws \InvalidArgumentException
     */
    public function getOne(QueryInterface $query = null)
    {
        if ($query === null) {
            $query = $this->createQuery();
        }

        if (!$query instanceof Query) {
            throw new \InvalidArgumentException('Query must be GeometriaLab\Mongo\Model\Query');
        }

        if (!$query->hasLimit()) {
            $query->limit(1);
        } else if ($query->getLimit() !== 1) {
            throw new \InvalidArgumentException('getOne accepts query with limit 1');
        }

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

        if (!$query instanceof Query) {
            throw new \InvalidArgumentException('Query must be GeometriaLab\Mongo\Model\Query');
        }

        $cursor = $this->find($query);

        $modelClass = $this->getModelClass();
        $collectionClass = $this->getCollectionClass();

        /**
         * @var CollectionInterface $collection
         */
        $collection = new $collectionClass();

        foreach($cursor as $document) {
            $data = $this->transformStorageDataForModel($document);

            /**
             * @var ModelInterface $model
             */
            $model = new $modelClass();
            $model->populate($data);
            $model->markClean();

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
     * @todo Refactor
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

        /**
         * @var \GeometriaLab\Model\Persistent\Schema\Property\PropertyInterface $property
         */
        foreach($model->getSchema()->getProperties() as $name => $property) {
            if ($property->isPersistent()) {
                $value = $model->get($name);

                if ($value !== null) {
                    if ($property instanceof ModelProperty) {
                        $value = $value->toArray(-1);
                    } else if ($property instanceof ArrayProperty && $property->getItemProperty() instanceof ModelProperty) {
                        foreach($value as &$item) {
                            $item = $item->toArray(-1);
                        }
                    }

                    $data[$name] = $value;
                }
                if ($property->isPrimary()) {
                    $primary[] = $name;
                }
            }
        }

        if (count($primary) !== 1 || $primary[0] !== 'id') {
            throw new \InvalidArgumentException("Mongo mapper support only one primary key 'id'");
        }

        $data = $this->transformModelDataForStorage($data);

        if (!isset($data['_id']) && $this->getPrimaryKeyGenerator()) {
            $data['_id'] = new \MongoId($this->getPrimaryKeyGenerator()->generate());
        }

        $result = $this->getMongoCollection()->insert($data, array('safe' => true));

        if ($result) {
            $model->set('id', $data['_id']->{'$id'});
            $model->markClean();

            return true;
        } else {
            return false;
        }
    }

    /**
     * Update model
     *
     * @todo Refactor
     * @param ModelInterface $model
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function update(ModelInterface $model)
    {
        if (!is_a($model, $this->getModelClass())) {
            throw new \InvalidArgumentException("Model must be {$this->getModelClass()}");
        }

        $setData = array();
        $unsetData = array();
        $primaryProperties = array();

        /**
         * @var \GeometriaLab\Model\Persistent\Schema\Property\PropertyInterface $property
         */
        foreach($model->getSchema()->getProperties() as $name => $property) {
            if ($property->isPersistent()) {
                if ($model->isPropertyChanged($name)) {
                    $value = $model->get($name);

                    if ($value === null) {
                        $unsetData[$name] = 1;
                    } else {
                        if ($property instanceof ModelProperty) {
                            $value = $value->toArray(-1);
                        } else if ($property instanceof ArrayProperty && $property->getItemProperty() instanceof ModelProperty) {
                            foreach($value as &$item) {
                                $item = $item->toArray(-1);
                            }
                        }

                        $setData[$name] = $value;
                    }
                }
                if ($property->isPrimary()) {
                    $primaryProperties[] = $name;
                }
            }
        }

        if (count($primaryProperties) !== 1 || $primaryProperties[0] !== 'id') {
            throw new \InvalidArgumentException("Mongo mapper support only one primary key 'id'");
        }

        $setData = $this->transformModelDataForStorage($setData);
        $unsetData = $this->transformModelDataForStorage($unsetData);

        if (empty($setData) && empty($unsetData)) {
            return false;
        }

        if (isset($data['_id'])) {
            $id = $model->getClean('id');
        } else {
            $id = $model->get('id');
        }

        if ($id === null) {
            throw new \InvalidArgumentException('Cant update model - primary property id is empty');
        }

        if (!empty($setData)) {
            $data['$set'] = $setData;
        }

        if (!empty($unsetData)) {
            $data['$unset'] = $unsetData;
        }

        $this->getMongoCollection()->update(array('_id' => new \MongoId($id)), $data);

        $model->markClean();

        return true;
    }

    /**
     * Delete model
     *
     * @param ModelInterface $model
     * @return boolean
     * @throws \InvalidArgumentException
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
            // Remove foreign relations
            foreach($model->getSchema()->getProperties() as $property) {
                if ($property instanceof AbstractHasRelation) {
                    $property->removeForeignRelations($model);
                }
            }

            $model->markClean(false);

            return true;
        } else {
            return false;
        }
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
     * Transform model data for storage
     *
     * @param array $data
     * @return array
     */
    protected function transformModelDataForStorage(array $data)
    {
        if (isset($data['id'])) {
            $data['_id'] = new \MongoId($data['id']);
        }
        unset($data['id']);

        return $data;
    }

    /**
     * Transform storage data for model
     *
     * @param array $data
     * @return array
     */
    protected function transformStorageDataForModel(array $data)
    {
        $data['id'] = $data['_id']->{'$id'};
        unset($data['_id']);

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
