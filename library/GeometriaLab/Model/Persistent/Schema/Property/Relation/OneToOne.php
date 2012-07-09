<?php

namespace GeometriaLab\Model\Persistent\Schema\Property\Relation;

class OneToOne extends AbstractRelation
{
    public function getForeignModel($model)
    {
        $referencedProperty = $model->get($this->getReferencedProperty());

        if ($referencedProperty === null) {
            return null;
        }

        $foreignMapper = call_user_func(array($this->getModelClass(), 'getMapper'));
        return $foreignMapper->getByCondition(array($this->getForeignProperty() => $referencedProperty));
    }

    public function setForeignModel($model, $value)
    {

    }

    public function prepare($value)
    {
        if (!is_a($value, $this->getModelClass())) {
            throw new \InvalidArgumentException("must be {$this->getModelClass()}");
        }

        return $value;
    }
}