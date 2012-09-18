<?php

namespace GeometriaLab\Model\Persistent\Schema\Property\Relation;

use GeometriaLab\Model\Persistent\CollectionInterface;

class HasMany extends AbstractHasRelation
{
    protected $relationClass = '\GeometriaLab\Model\Persistent\Relation\HasMany';
}