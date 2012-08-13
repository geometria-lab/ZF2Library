<?php

namespace GeometriaLab\Model\Persistent\Mapper;

use GeometriaLab\Model\Persistent\ModelInterface,
    GeometriaLab\Model\Persistent\CollectionInterface,
    GeometriaLab\Model\Persistent\Mapper\AbstractMapper,
    GeometriaLab\Model\Persistent\Mapper\QueryInterface,
    GeometriaLab\Model\Persistent\Schema\Property\Relation\HasOne as HasOneProperty,
    GeometriaLab\Model\Persistent\Schema\Property\Relation\HasMany as HasManyProperty;

class Mock extends AbstractMapper
{
    /**
     * @var CollectionInterface
     */
    protected $data;

    public function __construct(array $options = array())
    {
        parent::__construct($options);

        $collectionClass = $this->getCollectionClass();

        $this->data = new $collectionClass;
    }

    /**
     * @todo equals to mongo mapper. move to abstaract?
     * @param int $id
     * @return ModelInterface
     */
    public function get($id)
    {
        $query = $this->createQuery()->where(array('id' => $id));

        return $this->getOne($query);
    }

    /**
     * @todo equals to mongo mapper. move to abstaract?
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
            throw new \InvalidArgumentException('Query must be GeometriaLab\Model\Persistent\Mapper\Query');
        }

        if (!$query->hasLimit()) {
            $query->limit(1);
        } else if ($query->getLimit() !== 1) {
            throw new \InvalidArgumentException('getOne accepts query with limit 1');
        }

        return $this->getAll($query)->getFirst();
    }


    public function getAll(QueryInterface $query = null)
    {
        if ($query === null) {
            $query = $this->createQuery();
        }

        if (!$query instanceof Query) {
            throw new \InvalidArgumentException('Query must be GeometriaLab\Model\Persistent\Mapper\Query');
        }

        if ($query->hasSelect()) {
            throw new \InvalidArgumentException('Select not implemented yet');
        }

        if ($query->hasWhere()) {
            $collection = $this->data->getByCondition($query->getWhere());
        } else {
            $collection = clone $this->data;
        }

        if ($query->hasSort()) {
            $collection->sort($query->getSort());
        }

        if ($query->hasOffset()) {
            $collection = $collection->getSlice($query->getOffset());
        }

        if ($query->hasLimit()) {
            $collection = $collection->getSlice(0, $query->getLimit());
        }

        return $collection;
    }

    public function create(ModelInterface $model)
    {
        if (!is_a($model, $this->getModelClass())) {
            throw new \InvalidArgumentException("Model must be {$this->getModelClass()}");
        }

        // @todo check model has one integer id

        if ($model->id === null) {
            $model->id = count($this->data) + 1;
        }

        $this->data->push($model);

        $model->markClean();

        return true;
    }

    public function update(ModelInterface $model)
    {
        if (!is_a($model, $this->getModelClass())) {
            throw new \InvalidArgumentException("Model must be {$this->getModelClass()}");
        }

        foreach($this->data as $index => $storedModel) {
            if ($storedModel->id === $model->id) {
                $this->data->set($index, $model);

                $model->markClean();

                return true;
            }
        }

        return false;
    }

    public function delete(ModelInterface $model)
    {
        $id = $model->get('id');

        if ($id === null) {
            throw new \InvalidArgumentException('Cant delete model - primary property id is empty');
        }

        $this->data->removeByCondition(array('id' => $model->id));

        // Remove target relations
        foreach($model::getSchema()->getProperties() as $property) {
            if ($property instanceof HasOneProperty) {
                $model->getRelation($property->getName())->removeTargetRelation();
            } else if ($property instanceof HasManyProperty) {
                $model->getRelation($property->getName())->removeTargetRelations();
            }
        }

        $model->markClean(false);

        return true;
    }

    public function deleteByQuery(QueryInterface $query)
    {
        if (!$query instanceof Query) {
            throw new \InvalidArgumentException('Query must be GeometriaLab\Model\Persistent\Mapper\Query');
        }

        if ($query->hasSelect()) {
            throw new \InvalidArgumentException('Select not supported');
        }

        if ($query->hasSort()) {
            throw new \InvalidArgumentException('Sort not implemented yet');
        }

        if ($query->hasOffset()) {
            throw new \InvalidArgumentException('Offset not implemented yet');
        }

        if ($query->hasLimit()) {
            throw new \InvalidArgumentException('Limit not implemented yet');
        }

        if ($query->hasWhere()) {
            $this->data->removeByCondition($query->getWhere());
        } else {
            $this->data->clear();
        }
    }

    public function count(array $condition = array())
    {
        $query = $this->createQuery();
        $query->where($condition);

        if ($query->hasWhere()) {
            return $this->data->getByCondition($query)->count();
        } else {
            return $this->data->count();
        }
    }
}
