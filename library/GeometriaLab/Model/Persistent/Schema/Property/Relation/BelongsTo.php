<?php

namespace GeometriaLab\Model\Persistent\Schema\Property\Relation;

use GeometriaLab\Model\Persistent\ModelInterface;

class BelongsTo extends AbstractRelation
{
    protected $referencedModelClass;

    public function setReferencedModelClass($referencedModelClass)
    {
        $this->referencedModelClass = $referencedModelClass;

        return $this;
    }

    public function getReferencedModelClass()
    {
        return $this->referencedModelClass;
    }

    public function prepare($value)
    {
        if (!$value instanceof ModelInterface) {
            throw new \InvalidArgumentException('must implement GeometriaLab\Model\Persistent\ModelInterface');
        }

        return $value;
    }
}