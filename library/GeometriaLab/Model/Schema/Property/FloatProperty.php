<?php

namespace GeometriaLab\Model\Schema\Property;

class FloatProperty extends AbstractProperty
{
    /**
     * Validate property
     *
     * @param float $value
     * @return float
     * @throws \InvalidArgumentException
     */
    public function prepare($value)
    {
        if (!is_float($value)) {
            throw new \InvalidArgumentException("must be float");
        }

        return $value;
    }
}