<?php

namespace GeometriaLab\Model\Definition\Property;

class IntegerProperty extends AbstractProperty
{
    public function isValid($value)
    {
        return is_integer($value);
    }
}