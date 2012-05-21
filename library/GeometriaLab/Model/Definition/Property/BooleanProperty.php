<?php

namespace GeometriaLab\Model\Definition\Property;

class BooleanProperty extends AbstractProperty
{
    public function isValid($value)
    {
        return is_bool($value);
    }
}