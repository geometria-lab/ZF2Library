<?php

namespace GeometriaLab\Model\Persistent\Schema\Property\Relation;

use GeometriaLab\Model\Schema\Property\AbstractProperty;

abstract class AbstractRelation extends AbstractProperty
{
    protected $referencedProperty = 'id';

    protected $foreignProperty;

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

    public function setDefaultValue($value)
    {
        throw new \RuntimeException('You can\'t set default value for relation');
    }
}