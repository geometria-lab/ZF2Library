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
     * @throws \InvalidArgumentException
     */
    public function setModelClass($modelClass)
    {
        $reflect = new \ReflectionClass($modelClass);
        $implementsSchemaless = $reflect->implementsInterface('\GeometriaLab\Model\Schemaless\ModelInterface');
        $implementsPersistent = $reflect->implementsInterface('\GeometriaLab\Model\Persistent\ModelInterface');

        if (!$implementsSchemaless || $implementsPersistent) {
            throw new \InvalidArgumentException('Invalid model class, must be implements GeometriaLab\Model\Schemaless\ModelInterface');
        }

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