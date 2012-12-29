<?php

namespace GeometriaLab\Model\Persistent\Relation;

use GeometriaLab\Model\Persistent\ModelInterface,
    GeometriaLab\Model\Persistent\Schema\Property\Relation\HasOne as HasOneProperty,
    GeometriaLab\Model\Persistent\Collection;

class HasOne extends AbstractRelation
{
    /**
     * @var ModelInterface
     */
    protected $targetModel = false;

    /**
     * @param bool $refresh
     * @return ModelInterface|null
     */
    public function getTargetModel($refresh = false)
    {
        if ($refresh || $this->targetModel === false) {
            $originPropertyValue = $this->getOriginModel()->get($this->getProperty()->getOriginProperty());

            if ($originPropertyValue !== null) {
                $targetMapper = $this->getTargetMapper();
                $condition = array($this->getProperty()->getTargetProperty() => $originPropertyValue);
                $query = $targetMapper->createQuery()->where($condition);

                $this->targetModel = $targetMapper->getOne($query);
            } else {
                $this->targetModel = null;
            }
        }

        return $this->targetModel;
    }

    /**
     * @param ModelInterface $foreignModel
     * @return HasOne
     */
    public function setTargetModel(ModelInterface $foreignModel)
    {
        $this->targetModel = $foreignModel;

        return $this;
    }

    /**
     * @return HasOne
     */
    public function resetTargetModel()
    {
        $this->targetModel = false;

        return $this;
    }

    /**
     * Doe's it have target model?
     *
     * @return bool
     */
    public function hasTargetModel()
    {
        return $this->targetModel !== false;
    }

    /**
     * @return int
     */
    public function removeTargetRelation()
    {
        $onDelete = $this->getProperty()->getOnDelete();

        if ($onDelete === HasOneProperty::DELETE_NONE) {
            return 0;
        }

        $targetModel = $this->getTargetModel(true);

        if ($targetModel === null) {
            return 0;
        }

        if ($onDelete === HasOneProperty::DELETE_CASCADE) {
            $targetModel->delete();
        } else if ($onDelete === HasOneProperty::DELETE_SET_NULL) {
            $targetModel->set($this->getProperty()->getTargetProperty(), null);
            $targetModel->save();
        }

        return 1;
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

        foreach ($collection as $model) {
            /* @var $model \GeometriaLab\Model\Persistent\AbstractModel */
            $relation = $model->getRelation($this->getProperty()->getName());
            if ($relation instanceof HasMany) {
                $hasTargetModel = $relation->hasTargetModels();
            } else {
                $hasTargetModel = $relation->hasTargetModel();
            }
            if ($refresh || !$hasTargetModel) {
                // TODO '0' value will not pass check, should it?
                $value = $model->get($this->getProperty()->getOriginProperty());
                if ($value) {
                    $localModels[$value][] = $model;
                }
                $relation->resetTargetModel();
            }
        }

        if (count($localModels) == 0) {
            return;
        }

        $condition = array(
            $this->getProperty()->getTargetProperty() => array(
                '$in' => array_keys($localModels)
            )
        );

        $targetMapper = $this->getTargetMapper();
        $query = $targetMapper->createQuery()->where($condition);
        $targetModels = $targetMapper->getAll($query);

        if ($childRelations !== null) {
            $targetModels->fetchRelations($childRelations);
        }

        foreach ($targetModels as $targetModel) {
            /* @var ModelInterface $targetModel */
            $targetProperty = $this->getProperty()->getTargetProperty();
            $relationName = $this->getProperty()->getName();
            foreach ($localModels[$targetModel->get($targetProperty)] as $localModel) {
                /* @var ModelInterface $localModel */
                $localModel->set($relationName, $targetModel);
            }
        }
    }
}