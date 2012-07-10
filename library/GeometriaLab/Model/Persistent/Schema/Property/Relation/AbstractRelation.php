<?php

namespace GeometriaLab\Model\Persistent\Schema\Property\Relation;

use GeometriaLab\Model\Schema\Property\AbstractProperty;

abstract class AbstractRelation extends AbstractProperty
{
    protected $referencedProperty = 'id';

    protected $foreignProperty;

    protected $modelClass;

    public function setModelClass($modelClass)
    {
        $this->modelClass = $modelClass;

        return $this;
    }

    public function getModelClass()
    {
        return $this->modelClass;
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

    public function isPersistent()
    {
        return false;
    }
}