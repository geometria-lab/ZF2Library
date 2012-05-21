<?php

namespace GeometriaLab\Model\Definition\Property;

class FloatProperty extends AbstractProperty
{
    public function isValid($value)
    {
        return is_float($value);
    }
}