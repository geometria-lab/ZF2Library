<?php

namespace GeometriaLab\Model\Schema\Property;

use Zend\Validator\Callback as ZendValidatorCallback;

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

    protected function setup()
    {
        $property = $this;
        $this->getFilterChain()->attach(function($value) use ($property) {
            if (is_array($value) || (is_object($value) && $value instanceof \stdClass)) {
                /** @var \GeometriaLab\Model\Schemaless\ModelInterface $model */
                $modelClass = $property->getModelClass();
                $model = new $modelClass;

                $model->populate($value);

                $value = $model;
            }

            return $value;
        });

        $validator = new Validator\Model($this);
        $this->getValidatorChain()->addValidator($validator);
    }
}