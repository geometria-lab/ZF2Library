<?php

namespace GeometriaLab\Model\Persistent\Schema\Property\Relation;

use GeometriaLab\Model\Schema\Property\AbstractProperty;

abstract class AbstractRelation extends AbstractProperty
{
    protected $targetModelClass;

    protected $originProperty;

    protected $targetProperty;

    public function setRequired($required)
    {
        throw new \RuntimeException('Required is not supported for relations');
    }

    public function setTargetModelClass($referencedModelClass)
    {
        $this->targetModelClass = $referencedModelClass;

        return $this;
    }

    public function getTargetModelClass()
    {
        return $this->targetModelClass;
    }

    public function setTargetProperty($propertyName)
    {
        $this->targetProperty = $propertyName;

        return $this;
    }

    public function getTargetProperty()
    {
        return $this->targetProperty;
    }

    public function setOriginProperty($propertyName)
    {
        $this->originProperty = $propertyName;

        return $this;
    }

    public function getOriginProperty()
    {
        return $this->originProperty;
    }

    public function getRelationClass()
    {
        return $this->relationClass;
    }

    public function isPersistent()
    {
        return false;
    }

    public function setDefaultValue($value)
    {
        throw new \RuntimeException('You can\'t set default value for relation');
    }
}