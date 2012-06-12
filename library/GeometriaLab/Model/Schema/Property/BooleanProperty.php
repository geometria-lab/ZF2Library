<?php

namespace GeometriaLab\Model\Schema\Property;

class BooleanProperty extends AbstractProperty
{
    /**
     * Prepare value
     *
     * @param boolean $value
     * @return boolean mixed
     * @throws \InvalidArgumentException
     */
    public function prepare($value)
    {
        if (!is_bool($value)) {
            throw new \InvalidArgumentException();
        }

        return $value;
    }
}