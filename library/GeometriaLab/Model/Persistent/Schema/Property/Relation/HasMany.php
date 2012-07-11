<?php

namespace GeometriaLab\Model\Persistent\Schema\Property\Relation;

class HasMany extends HasOne
{
    public function getForeignModel($referencedModel)
    {
        $foreignMapper = call_user_func(array($this->getModelClass(), 'getMapper'));

        $referencedPropertyValue = $referencedModel->get($this->getReferencedProperty());

        if ($referencedPropertyValue === null) {
            return new $foreignMapper->getCollectionClass();
        }

        $query = $foreignMapper->createQuery();
        $query->where(array($this->getForeignProperty() => $referencedPropertyValue));

        return $foreignMapper->getAll($query);
    }

    public function prepare($value)
    {
        throw new \InvalidArgumentException("can't set foreign model to has many relation");
    }
}