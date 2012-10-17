<?php

namespace GeometriaLab\Model\Schema\Property\Validator\Exception;

class InvalidValueException extends \InvalidArgumentException
{
    /**
     * @var array
     */
    protected $validationErrorMessages;

    /**
     * Set validation error messages
     *
     * @param array() $validationErrorMessages
     * @return mixed
     */
    public function setValidationErrorMessages($validationErrorMessages)
    {
        $this->validationErrorMessages = $validationErrorMessages;
        return $this->validationErrorMessages;
    }

    /**
     * Get validation error messages
     *
     * @return array
     */
    public function getValidationErrorMessages()
    {
        return $this->validationErrorMessages;
    }
}
