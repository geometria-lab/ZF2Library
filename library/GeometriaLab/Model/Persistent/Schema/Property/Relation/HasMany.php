<?php

namespace GeometriaLab\Model\Persistent\Schema\Property\Relation;

use GeometriaLab\Model\Persistent\CollectionInterface;

class HasMany extends AbstractHasRelation
{
    public function getForeignModels($referencedModel)
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
        if (!$value instanceof CollectionInterface) {
            throw new \InvalidArgumentException("must be collection");
        }

        $model = $value->getFirst();

        if ($model && !is_a($model, $this->getModelClass())) {
            throw new \InvalidArgumentException("must be collection of {$this->getModelClass()}");
        }

        return $value;
    }
}