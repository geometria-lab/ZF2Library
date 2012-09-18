<?php

namespace GeometriaLab\Model\Persistent\Schema\Property\Relation;

use GeometriaLab\Model\Persistent\ModelInterface;

class HasOne extends AbstractHasRelation
{
    protected $relationClass = '\GeometriaLab\Model\Persistent\Relation\HasOne';
}