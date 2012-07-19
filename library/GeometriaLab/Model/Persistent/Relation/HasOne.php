<?php

namespace GeometriaLab\Model\Persistent\Relation;

use GeometriaLab\Model\Persistent\ModelInterface,
    GeometriaLab\Model\Persistent\Schema\Property\Relation\HasOne as HasOneProperty;

class HasOne extends AbstractHasRelation
{
    public function __construct(ModelInterface $referencedModel, HasOneProperty $property)
    {
        $this->setReferencedModel($referencedModel);
        $this->setProperty($property);
    }

    public function getForeignModel()
    {
        $referencedPropertyValue = $this->getReferencedModel()->get($this->getProperty()->getReferencedProperty());

        if ($referencedPropertyValue === null) {
            return null;
        }

        $foreignMapper = call_user_func(array($this->getProperty()->getForeignProperty(), 'getMapper'));
        return $foreignMapper->getByCondition(array($this->getForeignProperty() => $referencedPropertyValue));
    }
}