<?php

class GeometriaLab_Model_Definition_Property_Float extends GeometriaLab_Model_Definition_Property_Abstract
{
    protected function _isValid($value)
    {
        return is_float($value);
    }
}