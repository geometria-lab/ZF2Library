<?php

namespace GeometriaLab\Model\Definition\Property;

class StringProperty extends AbstractProperty
{
    public function isValid($value)
    {
        return is_string($value);
    }
}