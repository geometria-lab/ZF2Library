<?php

namespace GeometriaLab\Model\Persistent\Relation;

use GeometriaLab\Model\ModelInterface,
    GeometriaLab\Model\Persistent\Schema\Property\Relation\AbstractRelation as AbstractRelationProperty,
    GeometriaLab\Model\Persistent\Collection;

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
     * Set target objects to collection
     *
     * @param Collection $collection
     * @param bool $refresh
     * @param string $childRelations
     * @return void
     */
    abstract public function setTargetObjectsToCollection(Collection $collection, $refresh = false, $childRelations = null);
}