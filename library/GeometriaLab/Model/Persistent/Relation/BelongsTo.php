<?php

namespace GeometriaLab\Model\Persistent\Relation;

use GeometriaLab\Model\Persistent\ModelInterface,
    GeometriaLab\Model\Persistent\Collection;

class BelongsTo extends AbstractRelation
{
    /**
     * @var ModelInterface
     */
    protected $targetModel = false;

    /**
     * @param bool $refresh
     * @return ModelInterface|null
     * @throws \RuntimeException
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
     * @param ModelInterface $targetModel
     * @return BelongsTo
     * @throws \InvalidArgumentException
     */
    public function setTargetModel(ModelInterface $targetModel = null)
    {
        if ($targetModel !== null) {
            $targetPropertyValue = $targetModel->get($this->getProperty()->getTargetProperty());

            if ($targetPropertyValue === null) {
                throw new \InvalidArgumentException('Target property is null');
            }

            $this->targetModel = false;
        } else {
            $targetPropertyValue = null;

            $this->targetModel = null;
        }

        $originPropertyName = $this->getProperty()->getOriginProperty();

        if ($this->getOriginModel()->get($originPropertyName) !== $targetPropertyValue) {
            $this->getOriginModel()->set($originPropertyName, $targetPropertyValue);
        }

        return $this;
    }

    /**
     * @return BelongsTo
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
            if ($refresh || !$relation->hasTargetModel()) {
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
            /* @var $targetModel ModelInterface */
            foreach ($localModels[$targetModel->{$this->getProperty()->getTargetProperty()}] as $localModel) {
                $localModel->{$this->getProperty()->getName()} = $targetModel;
            }
        }
    }
}