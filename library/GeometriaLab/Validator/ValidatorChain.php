<?php

namespace GeometriaLab\Validator;

use Zend\Validator\ValidatorChain as ZendValidatorChain;

class ValidatorChain extends ZendValidatorChain
{
    /**
     * Cleanup error messages
     */
    public function cleanupMessages()
    {
        $this->messages = array();
    }
}
