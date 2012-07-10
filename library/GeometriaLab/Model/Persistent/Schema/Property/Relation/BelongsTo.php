<?php

namespace GeometriaLab\Model\Persistent\Schema\Property\Relation;

class BelongsTo extends AbstractRelation
{
    public function getReferencedModel($foreignModel)
    {
        $foreignPropertyValue = $foreignModel->get($this->getForeignProperty());

        if ($foreignPropertyValue === null) {
            return null;
        }

        $foreignMapper = call_user_func(array($this->getModelClass(), 'getMapper'));
        return $foreignMapper->getByCondition(array($this->getReferencedProperty() => $foreignPropertyValue));
    }

    public function setReferencedModel($foreignModel, $referencedModel)
    {
        $referencedPropertyValue = $referencedModel->get($this->getReferencedProperty());

        if ($referencedPropertyValue === null) {
            throw new \InvalidArgumentException('Referenced property is null');
        }

        $foreignModel->set($this->getForeignProperty(), $referencedPropertyValue);

        return $this;
    }

    public function prepare($value)
    {
        if (!is_a($value, $this->getModelClass())) {
            throw new \InvalidArgumentException("must be {$this->getModelClass()}");
        }

        return $value;
    }
}