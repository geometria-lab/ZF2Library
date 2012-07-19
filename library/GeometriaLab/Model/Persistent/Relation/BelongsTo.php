<?php

namespace GeometriaLab\Model\Persistent\Relation;

use GeometriaLab\Model\Persistent\ModelInterface,
    GeometriaLab\Model\Persistent\Schema\Property\Relation\BelongsTo as BelongsToProperty;

class BelongsTo
{
    public function __construct(ModelInterface $foreignModel, BelongsToProperty $property)
    {
        $this->setForeignModel($foreignModel);
        $this->setProperty($property);
    }

    public function getReferencedModel()
    {
        if ($this->referencedModel === null) {
            $foreignPropertyValue = $this->getForeignModel()->get($this->getProperty()->getForeignProperty());

            if ($foreignPropertyValue === null) {
                return null;
            }

            /**
             * @var \GeometriaLab\Model\Persistent\Mapper\MapperInterface $foreignMapper
             */
            $referencedMapper = call_user_func(array($this->getProperty()->getReferencedModelClass(), 'getMapper'));

            $condition = array($this->getProperty()->getReferencedProperty() => $foreignPropertyValue);
            $query = $referencedMapper->createQuery()->where($condition);

            $this->referencedModel = $referencedMapper->getOne($query);

            if ($this->referencedModel === null) {
                throw new \RuntimeException('Invalid referenced model with: ' . json_encode($condition));
            }
        }

        return $this->referencedModel;
    }

    public function setReferencedModel(ModelInterface $referencedModel = null)
    {
        if ($referencedModel !== null) {
            $referencedPropertyValue = $referencedModel->get($this->getProperty()->getReferencedProperty());

            if ($referencedPropertyValue === null) {
                throw new \InvalidArgumentException('Referenced property is null');
            }
        } else {
            $referencedPropertyValue = null;
        }

        $foreignPropertyName = $this->getProperty()->getForeignProperty();

        if ($this->getForeignModel()->get($foreignPropertyName) !== $referencedPropertyValue) {
            $this->getForeignModel()->set($foreignPropertyName, $referencedPropertyValue);
        }

        return $this;
    }
}