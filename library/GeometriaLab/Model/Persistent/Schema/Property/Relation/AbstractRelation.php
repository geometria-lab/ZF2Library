<?php

namespace GeometriaLab\Model\Persistent\Schema\Property\Relation;

use GeometriaLab\Model\Schema\Property\AbstractProperty,
    GeometriaLab\Model\Persistent\Schema\Property\PropertyInterface;

abstract class AbstractRelation extends AbstractProperty implements PropertyInterface
{
    protected $targetModelClass;

    protected $originProperty;

    protected $targetProperty;

    protected $relationClass;

    /**
     * @param $referencedModelClass
     * @return AbstractRelation
     */
    public function setTargetModelClass($referencedModelClass)
    {
        $this->targetModelClass = $referencedModelClass;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTargetModelClass()
    {
        return $this->targetModelClass;
    }

    /**
     * @param string $propertyName
     * @return AbstractRelation
     */
    public function setTargetProperty($propertyName)
    {
        $this->targetProperty = $propertyName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTargetProperty()
    {
        return $this->targetProperty;
    }

    /**
     * @param string $propertyName
     * @return AbstractRelation
     */
    public function setOriginProperty($propertyName)
    {
        $this->originProperty = $propertyName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOriginProperty()
    {
        return $this->originProperty;
    }

    /**
     * @return mixed
     */
    public function getRelationClass()
    {
        return $this->relationClass;
    }

    /**
     * @param bool $required
     * @return \GeometriaLab\Model\Schema\Property\PropertyInterface|void
     * @throws \RuntimeException
     */
    public function setRequired($required)
    {
        throw new \RuntimeException('Required is not supported for relations');
    }

    /**
     * @param bool $persistent
     * @return \GeometriaLab\Model\Persistent\Schema\Property\PropertyInterface|void
     * @throws \RuntimeException
     */
    public function setPersistent($persistent)
    {
        throw new \RuntimeException('Persistent is not supported for relations');
    }

    /**
     * @return bool
     */
    public function isPersistent()
    {
        return false;
    }

    /**
     * @param bool $primary
     * @return \GeometriaLab\Model\Persistent\Schema\Property\PropertyInterface|void
     * @throws \RuntimeException
     */
    public function setPrimary($primary)
    {
        throw new \RuntimeException('You can\'t set primary for relation');
    }

    /**
     * @return bool
     */
    public function isPrimary()
    {
        return false;
    }

    /**
     * @param $value
     * @return \GeometriaLab\Model\Schema\Property\AbstractProperty|void
     * @throws \RuntimeException
     */
    public function setDefaultValue($value)
    {
        throw new \RuntimeException('You can\'t set default value for relation');
    }
}