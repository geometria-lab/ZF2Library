<?php

namespace GeometriaLab\Model\Persistent\Schema\Property\Relation;

use GeometriaLab\Model\Persistent\CollectionInterface;

class HasMany extends AbstractHasRelation
{
    public function prepare($value)
    {
        if (!$value instanceof CollectionInterface) {
            throw new \InvalidArgumentException('must implements GeometriaLab\Model\Persistent\CollectionInterface');
        }

        $model = $value->getFirst();

        if ($model !== null && !is_a($model, $this->getForeignModelClass())) {
            throw new \InvalidArgumentException("must be collection of {$this->getForeignModelClass()}");
        }

        return $value;
    }
}