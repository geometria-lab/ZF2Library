<?php

namespace GeometriaLab\Model\Persistent\Schema\Property\Relation;

use GeometriaLab\Model\Persistent\ModelInterface;

class HasOne extends AbstractHasRelation
{
    public function prepare($value)
    {
        if (!$value instanceof ModelInterface) {
            throw new \InvalidArgumentException('must implement GeometriaLab\Model\Persistent\ModelInterface');
        }

        return $value;
    }
}