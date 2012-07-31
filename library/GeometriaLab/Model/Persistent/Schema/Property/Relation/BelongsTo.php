<?php

namespace GeometriaLab\Model\Persistent\Schema\Property\Relation;

use GeometriaLab\Model\Persistent\ModelInterface;

class BelongsTo extends AbstractRelation
{
    protected $targetProperty = 'id';

    protected $relationClass = '\GeometriaLab\Model\Persistent\Relation\BelongsTo';

    public function prepare($value)
    {
        if (!$value instanceof ModelInterface) {
            throw new \InvalidArgumentException('must implement GeometriaLab\Model\Persistent\ModelInterface');
        }

        return $value;
    }
}