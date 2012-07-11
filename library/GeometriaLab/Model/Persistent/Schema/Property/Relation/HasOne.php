<?php

namespace GeometriaLab\Model\Persistent\Schema\Property\Relation;

class HasOne extends AbstractHasRelation
{
    public function getForeignModel($referencedModel)
    {
        $referencedPropertyValue = $referencedModel->get($this->getReferencedProperty());

        if ($referencedPropertyValue === null) {
            return null;
        }

        $foreignMapper = call_user_func(array($this->getModelClass(), 'getMapper'));
        return $foreignMapper->getByCondition(array($this->getForeignProperty() => $referencedPropertyValue));
    }

    public function prepare($value)
    {
        if (!is_a($value, $this->getModelClass())) {
            throw new \InvalidArgumentException("must be {$this->getModelClass()}");
        }

        return $value;
    }
}