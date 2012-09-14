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
     * @var \Closure
     */
    protected static $filterValidator;

    /**
     * @var ZendValidatorCallback
     */
    protected static $modelValidator;

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
        if (self::$modelFilter === null) {
            $property = $this;
            self::$modelFilter = function($value) use ($property) {
                if (is_array($value) || (is_object($value) && $value instanceof \stdClass)) {
                    /** @var \GeometriaLab\Model\Schemaless\ModelInterface $model */
                    $model = new $property->getModelClass();

                    if ($model instanceof \GeometriaLab\Model\ModelInterface) {
                        /** @var \GeometriaLab\Model\ModelInterface $model */
                        $model->populateSilent($value);
                    } else {
                        /** @var \GeometriaLab\Model\Schemaless\ModelInterface $model */
                        $model->populate($value);
                    }

                    $value = $model;
                }

                return $value;
            };
        }

        $this->getFilterChain()->attach(self::$modelFilter);

        if (self::$modelValidator === null) {
            $validator = new ZendValidatorCallback();
            $validator->setOptions(array(
                'messageTemplates' => array(
                    ZendValidatorCallback::INVALID_VALUE => "Value must be a model object of '{$this->getModelClass()}'",
                ),
            ));
            $property = $this;
            $validator->setCallback(function($value) use ($property) {
                if (!is_object($value) || !is_a($value, $property->getModelClass())) {
                    return false;
                }

                if ($value instanceof \GeometriaLab\Model\ModelInterface) {
                    /** @var \GeometriaLab\Model\ModelInterface $model */
                    return $value->isValid();
                }

                return true;
            });

            self::$modelValidator = $validator;
        }

        $this->getValidatorChain()->addValidator(self::$modelValidator);
    }
}