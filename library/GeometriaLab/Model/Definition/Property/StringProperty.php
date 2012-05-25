<?php

namespace GeometriaLab\Model\Definition\Property;

class StringProperty extends AbstractProperty
{
    /**
     * Prepare value
     *
     * @param string $value
     * @return string
     * @throws \InvalidArgumentException
     */
    public function prepare($value)
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException();
        }

        return $value;
    }
}