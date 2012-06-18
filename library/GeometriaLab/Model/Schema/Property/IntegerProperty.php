<?php

namespace GeometriaLab\Model\Schema\Property;

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
            throw new \InvalidArgumentException("must be integer");
        }

        return $value;
    }
}