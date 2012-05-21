<?php

namespace GeometriaLab\Model\Definition\Property;

class ModelProperty extends AbstractProperty
{
    /**
     * @var \GeometriaLab\Model\Definition
     */
    protected $modelDefinition;

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

    public function isValid($value)
    {
        return is_a($value, $this->getModelDefinition()->getClassName());
    }
}