<?php

namespace GeometriaLab\Model\Persistent\Schema\Property\Relation;

use GeometriaLab\Model\Persistent\ModelInterface,
    GeometriaLab\Model\Persistent\CollectionInterface;

class BelongsTo extends AbstractRelation
{
    public function getReferencedModel(ModelInterface $foreignModel)
    {
        $foreignPropertyValue = $foreignModel->get($this->getForeignProperty());

        if ($foreignPropertyValue === null) {
            return null;
        }

        $foreignMapper = call_user_func(array($this->getModelClass(), 'getMapper'));
        return $foreignMapper->getByCondition(array($this->getReferencedProperty() => $foreignPropertyValue));
    }

    public function setReferencedModel(ModelInterface $foreignModel, ModelInterface $referencedModel = null)
    {
        if ($referencedModel !== null) {
            $referencedPropertyValue = $referencedModel->get($this->getReferencedProperty());

            if ($referencedPropertyValue === null) {
                throw new \InvalidArgumentException('Referenced property is null');
            }
        } else {
            $referencedPropertyValue = null;
        }

        if ($foreignModel->get($this->getForeignProperty()) !== $referencedPropertyValue) {
            $foreignModel->set($this->getForeignProperty(), $referencedPropertyValue);
        }

        return $this;
    }

    public function setReferencedModelsToCollection(CollectionInterface $collection, $refresh = false, $foreignObjectContext = null)
    {
        // collect foregn keys values
        $localModels = array();
        foreach ($collection as $model) {
            // check that key is set in model
            // TODO 0 value will not pass check, should it ?
            if ($model->{$this->_localKey}) {
                if ($refresh || !$this->_issetModelForeignObject($model)) {
                    $localModels[$model->{$this->_localKey}][] = $model;
                    $model->getRelation($this->_relationName)->setForeignObjectNotFound();
                }
            }
        }

        // fetch foreign models
        $fetchParams = array($this->_foreignKey => array_keys($localModels));
        $foreignObjects = $this->_getForeignMapper()->fetchAll($fetchParams);
        if ($foreignObjects instanceof Geometria_Model_Collection_ExtendedInterface) {
            $foreignObjects->onFetchAsRelation($foreignObjectContext);
        }
        // set foreign objects in collection models
        foreach ($foreignObjects as $foreignObject) {
            foreach ($localModels[$foreignObject->{$this->_foreignKey}] as $localModel) {
                $localModel->{$this->_relationName} = $foreignObject;
            }
        }
    }

    public function prepare($value)
    {
        if (!is_a($value, $this->getModelClass())) {
            throw new \InvalidArgumentException("must be {$this->getModelClass()}");
        }

        return $value;
    }
}