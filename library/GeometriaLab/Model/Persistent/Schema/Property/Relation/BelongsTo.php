<?php

namespace GeometriaLab\Model\Persistent\Schema\Property\Relation;

use GeometriaLab\Model\Persistent\ModelInterface;

class BelongsTo extends AbstractRelation
{
    protected $targetProperty = 'id';

    protected $relationClass = '\GeometriaLab\Model\Persistent\Relation\BelongsTo';
}