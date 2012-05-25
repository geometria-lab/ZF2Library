<?php

namespace GeometriaLab\Model\Definition\Property;

class IntegerProperty extends AbstractProperty
{
    /**
     * Prepare value
     *
     * @param integer $value
     * @return integer
     * @throws \InvalidArgumentException
     */
    public function prepare($value)
    {
        if (!is_integer($value)) {
            throw new \InvalidArgumentException();
        }

        return $value;
    }
}