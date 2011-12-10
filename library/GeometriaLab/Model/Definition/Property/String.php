<?php

class GeometriaLab_Model_Definition_Property_String extends GeometriaLab_Model_Definition_Property_Abstract
{
    protected function _isValid($value)
    {
        return is_string($value);
    }
}