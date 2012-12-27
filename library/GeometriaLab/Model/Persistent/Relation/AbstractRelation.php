<?php

namespace GeometriaLab\Model\Persistent\Relation;

use GeometriaLab\Model\ModelInterface,
    GeometriaLab\Model\Persistent\Schema\Property\Relation\AbstractRelation as AbstractRelationProperty,
    GeometriaLab\Model\Persistent\Collection,
    GeometriaLab\Model\Persistent\Mapper\MapperInterface;

abstract class AbstractRelation
{
    /**
     * @var AbstractRelationProperty
     */
    protected $property;

    /**
     * @var ModelInterface
     */
    protected $originModel;

    /**
     * @param ModelInterface $model
     * @param AbstractRelationProperty $property
     */
    public function __construct(ModelInterface $originModel, AbstractRelationProperty $property)
    {
        $this->setOriginModel($originModel);
        $this->setProperty($property);
    }

    /**
     * @param AbstractRelationProperty $property
     * @return AbstractRelation
     */
    public function setProperty(AbstractRelationProperty $property)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * @return AbstractRelationProperty
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * @param ModelInterface $model
     * @return AbstractRelation
     */
    public function setOriginModel(ModelInterface $model)
    {
        $this->originModel = $model;

        return $this;
    }

    /**
     * @return ModelInterface
     */
    public function getOriginModel()
    {
        return $this->originModel;
    }

    /**
     * Get target mapper object
     *
     * @return MapperInterface
     */
    public function getTargetMapper()
    {
        return call_user_func(array($this->getProperty()->getTargetModelClass(), 'getMapper'));
    }

    /**
     * Set target objects to collection models
     *
     * @param Collection $collection
     * @param bool $refresh
     * @param string $childRelations
     * @return void
     */
    abstract public function setTargetObjectsToCollection(Collection $collection, $refresh = false, $childRelations = null);
}