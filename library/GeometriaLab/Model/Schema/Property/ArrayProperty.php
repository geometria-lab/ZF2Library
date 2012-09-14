<?php

namespace GeometriaLab\Model\Schema\Property;

use Zend\Validator\Callback as ZendValidatorCallback;

class ArrayProperty extends AbstractProperty
{
    /**
     * @var PropertyInterface
     */
    protected $itemProperty;

    /**
     * @var ZendValidatorCallback
     */
    protected static $isArrayValidator;

    /**
     * Set item property
     *
     * @param PropertyInterface $property
     * @return ArrayProperty
     */
    public function setItemProperty(PropertyInterface $property)
    {
        $this->itemProperty = $property;

        return $this;
    }

    /**
     * Get item property
     *
     * @return PropertyInterface
     */
    public function getItemProperty()
    {
        return $this->itemProperty;
    }

    protected function setup()
    {
        $this->addTypeValidator('array');

        if (!isset(static::$isArrayValidator)) {
            $validator = new ZendValidatorCallback();
            $validator->setOptions(array(
                'messageTemplates' => array(
                    ZendValidatorCallback::INVALID_VALUE => "Value must be a array of type '%type%'",
                ),
                'messageVariables' => array(
                    'type' => 'type'
                )
            ));

            $property = $this;
            $validator->setCallback(function($value) use ($property, $validator) {
                if ($property->getItemProperty() === null) {
                    return true;
                }

                $isValid = true;
                foreach($value as $item) {
                    if (!$property->getItemProperty()->getValidatorChain()->isValid($item)) {
                        $isValid = false;
                        break;
                    }
                }

                if (!$isValid) {
                    $type = str_replace('Property', '', get_class($property->getItemProperty()));
                    $validator->type = strtolower($type);
                }

                return $isValid;
            });

            static::$isArrayValidator = $validator;
        }

        $this->getValidatorChain()->addValidator(static::$isArrayValidator);
    }
}