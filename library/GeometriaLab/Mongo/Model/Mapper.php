<?php

namespace GeometriaLab\Mongo\Model;

use GeometriaLab\Mongo,
    GeometriaLab\Model\Persistent\ModelInterface,
    GeometriaLab\Model\Persistent\CollectionInterface,
    GeometriaLab\Model\Persistent\Mapper\AbstractMapper,
    GeometriaLab\Model\Persistent\Mapper\QueryInterface,
    GeometriaLab\Model\Persistent\Schema\Property\ArrayProperty,
    GeometriaLab\Model\Persistent\Schema\Property\ModelProperty,
    GeometriaLab\Model\Persistent\Schema\Property\Relation\HasOne as HasOneProperty,
    GeometriaLab\Model\Persistent\Schema\Property\Relation\HasMany as HasManyProperty;

use Zend\ServiceManager\ServiceManager as ZendServiceManager;

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
     * Flag for validate properties when fetch
     *
     * @var bool
     */
    protected $validateOnFetch = false;

    /**
     * @var ZendServiceManager
     */
    static protected $serviceManager;

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
     * Set validate on fetch
     *
     * @param bool $validateOnFetch
     */
    public function setValidateOnFetch($validateOnFetch)
    {
        $this->validateOnFetch = (bool)$validateOnFetch;
    }

    /**
     * Get validate on fetch
     *
     * @return bool
     */
    public function getValidateOnFetch()
    {
        return $this->validateOnFetch;
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
     * @throws \InvalidArgumentException
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
            $model->populate($data, !$this->getValidateOnFetch());
            $model->markClean();

            $collection->push($model);
        }

        $cursor->reset();

        return $collection;
    }

    /**
     * Find mongo documents by query
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
     * @throws \RuntimeException
     */
    public function create(ModelInterface $model)
    {
        if (!is_a($model, $this->getModelClass())) {
            throw new \InvalidArgumentException("Model must be {$this->getModelClass()}");
        }

        if (!$model->isValid()) {
            $errorString = '';
            foreach ($model->getErrorMessages() as $fieldName => $errors) {
                $errorString .= "Field $fieldName:\r\n" . implode("\r\n", $errors) . "\r\n";
            }
            throw new \RuntimeException("Model is invalid: $errorString");
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
                    // @todo Wrap originProperty and targetProperty to mongoId
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

        // @todo id must be string

        if (count($primary) !== 1 || $primary[0] !== 'id') {
            throw new \InvalidArgumentException("Mongo mapper support only one primary key 'id'");
        }

        $data = $this->transformModelDataForStorage($data);

        if (!isset($data['_id']) && $this->getPrimaryKeyGenerator()) {
            $data['_id'] = $this->getPrimaryKeyGenerator()->generate();
        }

        $result = $this->getMongoCollection()->insert($data, array('safe' => true));

        if ($result) {
            $model->set('id', (string)$data['_id']);
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
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function update(ModelInterface $model)
    {
        if (!is_a($model, $this->getModelClass())) {
            throw new \InvalidArgumentException("Model must be {$this->getModelClass()}");
        }

        if (!$model->isValid()) {
            $errorString = '';
            foreach ($model->getErrorMessages() as $fieldName => $errors) {
                $errorString .= "Field $fieldName:\r\n" . implode("\r\n", $errors) . "\r\n";
            }
            throw new \RuntimeException("Model is invalid: $errorString");
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
                        // @todo Wrap originProperty and targetProperty to mongoId
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

        $condition = $this->transformModelDataForStorage(array('id' => $id));

        $this->getMongoCollection()->update($condition, $data);

        $model->markClean();

        return true;
    }

    /**
     * Delete model
     *
     * @todo Refactor
     * @param ModelInterface $model
     * @return boolean
     * @throws \InvalidArgumentException
     */
    public function delete(ModelInterface $model)
    {
        if (!is_a($model, $this->getModelClass())) {
            throw new \InvalidArgumentException("Model must be {$this->getModelClass()}");
        }

        $id = $model->get('id');

        if ($id === null) {
            throw new \InvalidArgumentException('Cant delete model - primary property id is empty');
        }

        $condition = $this->transformModelDataForStorage(array('id' => $id));
        $result = $this->getMongoCollection()->remove($condition, array('safe' => true));

        if ($result) {
            // Remove target relations\
            // @todo implement via events
            foreach($model->getSchema()->getProperties() as $property) {
                if ($property instanceof HasOneProperty) {
                    $model->getRelation($property->getName())->removeTargetRelation();
                } else if ($property instanceof HasManyProperty) {
                    $model->getRelation($property->getName())->removeTargetRelations();
                }
            }

            $model->markClean(false);

            return true;
        } else {
            return false;
        }
    }

    public function deleteByQuery(QueryInterface $query)
    {
        if (!$query instanceof Query) {
            throw new \InvalidArgumentException('Query must be GeometriaLab\Mongo\Model\Query');
        }

        if ($query->hasSelect()) {
            throw new \InvalidArgumentException('Select not supported');
        }

        if ($query->hasSort()) {
            throw new \InvalidArgumentException('Sort not supported');
        }

        if ($query->hasOffset()) {
            throw new \InvalidArgumentException('Offset not supported');
        }

        if ($query->hasLimit()) {
            throw new \InvalidArgumentException('Limit not supported');
        }

        if ($query->hasWhere()) {
            $where = $this->transformModelDataForStorage($query->getWhere());
        } else {
            $where = array();
        }

        return $this->getMongoCollection()->remove($where);
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
     * Set Service Manager
     *
     * @param ZendServiceManager $serviceManager
     */
    static public function setServiceManager(ZendServiceManager $serviceManager)
    {
        static::$serviceManager = $serviceManager;
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
            if ((string)$data['_id'] != $data['id']) {
                $data['_id'] = $data['id'];
            }
            unset($data['id']);
        }

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
        $data['id'] = (string)$data['_id'];
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
        return self::$serviceManager->get('MongoManager')->get($this->getMongoInstanceName());
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
