<?php

namespace GeometriaLab\Model\Schema\Property;

class ModelProperty extends AbstractProperty
{
    /**
     * @var string
     */
    protected $modelClass;

    /**
     * Set model class
     *
     * @param string $modelClass
     * @return ModelProperty
     */
    public function setModelClass($modelClass)
    {
        $this->modelClass = $modelClass;

        return $this;
    }

    /**
     * @return string
     */
    public function getModelClass()
    {
        return $this->modelClass;
    }

    /**
     * Prepare value
     *
     * @param array|\Traversable|\stdClass|\GeometriaLab\Model\ModelInterface $value
     * @return \GeometriaLab\Model\ModelInterface
     */
    public function prepare($value)
    {
        if (is_a($value, $this->getModelClass())) {
            return $value;
        } else {
            $className = $this->getModelClass();
            return new $className($value);
        }
    }
}