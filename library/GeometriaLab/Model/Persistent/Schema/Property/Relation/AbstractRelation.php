<?php

namespace GeometriaLab\Model\Persistent\Schema\Property\Relation;

use GeometriaLab\Model\Schema\Property\AbstractProperty;

abstract class AbstractRelation extends AbstractProperty
{
    CONST DELETE_NONE = 'none';
    CONST DELETE_SET_NULL = 'setNull';
    CONST DELETE_CASCADE = 'cascade';

    protected $referencedProperty;

    protected $foreignProperty;

    protected $modelClass;

    protected $deleteMode;

    public function setModelClass($modelClass)
    {
        $this->modelClass = $modelClass;

        return $this;
    }

    public function getModelClass()
    {
        return $this->modelClass;
    }

    public function setDeleteMode($deleteMode)
    {
        $this->deleteMode = $deleteMode;

        return $this;
    }

    public function getDeleteMode()
    {
        return $this->deleteMode;
    }

    public function setForeignProperty($propertyName)
    {
        $this->foreignProperty = $propertyName;

        return $this;
    }

    public function getForeignProperty()
    {
        return $this->foreignProperty;
    }

    public function setReferencedProperty($propertyName)
    {
        $this->referencedProperty = $propertyName;

        return $this;
    }

    public function getReferencedProperty()
    {
        return $this->referencedProperty;
    }
}