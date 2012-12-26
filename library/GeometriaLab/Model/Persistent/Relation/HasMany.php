<?php

namespace GeometriaLab\Model\Persistent\Relation;

use GeometriaLab\Model\Persistent\CollectionInterface,
    GeometriaLab\Model\Persistent\Collection,
    GeometriaLab\Model\Persistent\Schema\Property\Relation\HasMany as HasManyProperty;

class HasMany extends AbstractRelation
{
    /**
     * @var CollectionInterface
     */
    protected $targetModels = false;

    /**
     * @param bool $refresh
     * @return CollectionInterface
     */
    public function getTargetModels($refresh = false)
    {
        if ($refresh || $this->targetModels === false) {
            $targetMapper = call_user_func(array($this->getProperty()->getTargetModelClass(), 'getMapper'));

            $originPropertyValue = $this->getOriginModel()->get($this->getProperty()->getOriginProperty());

            if ($originPropertyValue === null) {
                $collectionClass = $targetMapper->getCollectionClass();
                $this->targetModels = new $collectionClass;
            } else {
                $query = $targetMapper->createQuery();
                $query->where(array($this->getProperty()->getTargetProperty() => $originPropertyValue));

                $this->targetModels = $targetMapper->getAll($query);
            }
        }

        return $this->targetModels;
    }

    /**
     * @param CollectionInterface $collection
     * @return HasMany
     */
    public function setTargetModels(CollectionInterface $collection)
    {
        $this->targetModels = $collection;

        return $this;
    }

    /**
     * @return HasMany
     */
    public function resetTargetModel()
    {
        $this->targetModels = false;

        return $this;
    }

    /**
     * Doe's it have target models?
     *
     * @return bool
     */
    public function hasTargetModels()
    {
        return $this->targetModels !== false;
    }

    /**
     * @return int
     */
    public function removeTargetRelations()
    {
        $onDelete = $this->getProperty()->getOnDelete();

        if ($onDelete === HasManyProperty::DELETE_NONE) {
            return 0;
        }

        $targetModels = $this->getTargetModels(true);

        if ($targetModels->isEmpty()) {
            return 0;
        }

        foreach($targetModels as $targetModel) {
            if ($onDelete === HasManyProperty::DELETE_CASCADE) {
                $targetModel->delete();
            } elseif ($onDelete === HasManyProperty::DELETE_SET_NULL) {
                $targetModel->set($this->getProperty()->getTargetProperty(), null);
                $targetModel->save();
            }
        }

        return count($targetModels);
    }

    public function setTargetObjectsToCollection(Collection $collection, $refresh = false, $childRelations = null)
    {
        $localModels = array();
        $fetchValues = array();
        foreach ($collection as $model) {
            /* @var $model \GeometriaLab\Model\Persistent\AbstractModel */
            // TODO 0 value will not pass check, should it ?
            $values = $model->get($this->getProperty()->getOriginProperty());
            if ($values) {
                $relation = $model->getRelation($this->getProperty()->getName());
                if ($refresh || !$relation->hasTargetModel()) {
                    $localModels[] = array(
                        'model'  => $model,
                        'values' => (array) $values,
                    );
                    $fetchValues = array_merge($fetchValues, $values);
                }
            }
        }

        if (0 == count($fetchValues)) {
            return;
        }

        $condition = array(
            $this->getProperty()->getTargetProperty() => array(
                '$in' => array_values(array_unique($localModels))
            )
        );

        /* @var \GeometriaLab\Model\Persistent\Mapper\MapperInterface $targetMapper */
        $targetMapper = call_user_func(array($this->getProperty()->getTargetModelClass(), 'getMapper'));
        $query = $targetMapper->createQuery()->where($condition);
        $targetModels = $targetMapper->getAll($query);

        if ($childRelations !== null) {
            $targetModels->fetchRelations($childRelations);
        }

        $targetCollectionClass = get_class($targetModels);

        foreach ($localModels as $localModel) {
            /* @var CollectionInterface $targetCollection */
            $targetCollection = new $targetCollectionClass();
            foreach ($targetModels as $targetModel) {
                if (in_array($targetModel->{$this->getProperty()->getTargetProperty()}, $localModel['values'])) {
                    $targetCollection->push($targetModel);
                }
            }
            $localModel['model']->{$this->getProperty()->getName()} = $targetCollection;
        }
    }
}