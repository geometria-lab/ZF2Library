<?php

namespace GeometriaLab\Model\Persistent\Schema\Property\Relation;

use GeometriaLab\Model\Persistent\ModelInterface;

abstract class AbstractHasRelation extends AbstractRelation
{
    CONST DELETE_NONE     = 'none';
    CONST DELETE_SET_NULL = 'setNull';
    CONST DELETE_CASCADE  = 'cascade';

    protected $foreignModelClass;

    protected $onDelete = 'setNull';

    public function setOnDelete($deleteMode)
    {
        $this->onDelete = $deleteMode;

        return $this;
    }

    public function getOnDelete()
    {
        return $this->onDelete;
    }

    public function setForeignModelClass($foreignModelClass)
    {
        $this->foreignModelClass = $foreignModelClass;

        return $this;
    }

    public function getForeignModelClass()
    {
        return $this->foreignModelClass;
    }
}