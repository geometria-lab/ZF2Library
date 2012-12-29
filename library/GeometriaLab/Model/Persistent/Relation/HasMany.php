<?php

namespace GeometriaLab\Model\Persistent\Relation;

use GeometriaLab\Model\Persistent\CollectionInterface,
    GeometriaLab\Model\Persistent\Collection,
    GeometriaLab\Model\Persistent\Schema\Property\Relation\HasMany as HasManyProperty,
    GeometriaLab\Model\Persistent\ModelInterface;

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
            $targetMapper = $this->getTargetMapper();
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

    /**
     * Set target objects to collection models
     *
     * @param Collection $collection
     * @param bool $refresh
     * @param string $childRelations
     * @return void
     */
    public function setTargetObjectsToCollection(Collection $collection, $refresh = false, $childRelations = null)
    {
        $localModels = array();
        $fetchValues = array();

        foreach ($collection as $model) {
            /* @var $model \GeometriaLab\Model\Persistent\AbstractModel */
            // TODO 0 value will not pass check, should it ?
            $values = (array) $model->get($this->getProperty()->getOriginProperty());
            if ($values) {
                $relation = $model->getRelation($this->getProperty()->getName());
                if ($relation instanceof HasMany) {
                    $hasTargetModel = $relation->hasTargetModels();
                } else {
                    $hasTargetModel = $relation->hasTargetModel();
                }
                if ($refresh || !$hasTargetModel) {
                    $localModels[] = array(
                        'model'  => $model,
                        'values' => $values,
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
                '$in' => array_values(array_unique($fetchValues))
            )
        );

        $targetMapper = $this->getTargetMapper();
        $query = $targetMapper->createQuery()->where($condition);
        $targetModels = $targetMapper->getAll($query);

        if ($childRelations !== null) {
            $targetModels->fetchRelations($childRelations);
        }

        $targetCollectionClass = get_class($targetModels);

        foreach ($localModels as $localModel) {
            /* @var CollectionInterface $targetCollection */
            $targetCollection = new $targetCollectionClass();
            $targetProperty = $this->getProperty()->getTargetProperty();
            foreach ($targetModels as $targetModel) {
                /* @var ModelInterface $targetModel */
                if (in_array($targetModel->get($targetProperty), $localModel['values'])) {
                    $targetCollection->push($targetModel);
                }
            }
            $relationName = $this->getProperty()->getName();
            $localModel['model']->set($relationName, $targetCollection);
        }
    }
}