<?php

namespace GeometriaLab\Model\Persistent\Relation;

use GeometriaLab\Model\Persistent\ModelInterface,
    GeometriaLab\Model\Persistent\Schema\Property\Relation\AbstractRelation as AbstractRelationProperty;

abstract class AbstractRelation
{
    /**
     * @var AbstractRelationProperty
     */
    protected $property;

    /**
     * @var ModelInterface
     */
    protected $foreignModel;

    /**
     * @var ModelInterface
     */
    protected $referencedModel;

    /**
     * @param ModelInterface $foreignModel
     * @return BelongsTo
     */
    public function setForeignModel(ModelInterface $foreignModel)
    {
        $this->foreignModel = $foreignModel;

        return $this;
    }

    /**
     * @return ModelInterface
     */
    public function getForeignModel()
    {
        return $this->foreignModel;
    }

    /**
     * @param ModelInterface $referencedModel
     * @return AbstractRelation
     */
    public function setReferencedModel($referencedModel)
    {
        $this->referencedModel = $referencedModel;

        return $this;
    }

    /**
     * @return ModelInterface
     */
    public function getReferencedModel()
    {
        return $this->referencedModel;
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
}