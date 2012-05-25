<?php

namespace GeometriaLab\Model\Definition\Property;

class ModelProperty extends AbstractProperty
{
    /**
     * @var \GeometriaLab\Model\Definition
     */
    protected $modelDefinition;

    /**
     * Set model definition
     *
     * @param \GeometriaLab\Model\Definition $modelDefinition
     * @return ModelProperty
     */
    public function setModelDefinition(\GeometriaLab\Model\Definition $modelDefinition)
    {
        $this->modelDefinition = $modelDefinition;

        return $this;
    }

    /**
     * @return \GeometriaLab\Model\Definition
     */
    public function getModelDefinition()
    {
        return $this->modelDefinition;
    }

    /**
     * Prepare value
     *
     * @param array|\Traversable|\stdClass|\GeometriaLab\Model\ModelInterface $value
     * @return \GeometriaLab\Model\ModelInterface
     */
    public function prepare($value)
    {
        if (is_a($value, $this->getModelDefinition()->getClassName())) {
            return $value;
        } else {
            $className = $this->getModelDefinition()->getClassName();
            return new $className($value);
        }
    }
}