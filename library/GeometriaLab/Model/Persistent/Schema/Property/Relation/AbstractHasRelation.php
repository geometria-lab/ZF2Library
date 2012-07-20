<?php

namespace GeometriaLab\Model\Persistent\Schema\Property\Relation;

use GeometriaLab\Model\Persistent\ModelInterface;

abstract class AbstractHasRelation extends AbstractRelation
{
    CONST DELETE_NONE     = 'none';
    CONST DELETE_SET_NULL = 'setNull';
    CONST DELETE_CASCADE  = 'cascade';

    protected $onDelete = 'setNull';

    protected $originProperty = 'id';

    public function setOnDelete($deleteMode)
    {
        if ($deleteMode !== static::DELETE_NONE &&
            $deleteMode !== static::DELETE_SET_NULL &&
            $deleteMode !== static::DELETE_CASCADE) {
            throw new \InvalidArgumentException("Invalid onDelete '$deleteMode' mode");
        }

        $this->onDelete = $deleteMode;

        return $this;
    }

    public function getOnDelete()
    {
        return $this->onDelete;
    }
}