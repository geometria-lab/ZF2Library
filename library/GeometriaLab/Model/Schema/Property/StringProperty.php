<?php

namespace GeometriaLab\Model\Schema\Property;

use Zend\Validator\Callback as ZendValidatorCallback;

class StringProperty extends AbstractProperty
{
    /**
     * @var ZendValidatorCallback
     */
    static protected $isStringValidator;

    public function setup()
    {
        if (static::$isStringValidator === null) {
            $validator = new ZendValidatorCallback();
            $validator->setOptions(array(
                'messageTemplates' => array(
                    ZendValidatorCallback::INVALID_VALUE => 'Value must be a string',
                ),
            ));
            $validator->setCallback(array(null, 'is_string'));

            static::$isStringValidator = $validator;
        }

        $this->getValidatorChain()->addValidator(static::$isStringValidator);
    }
}