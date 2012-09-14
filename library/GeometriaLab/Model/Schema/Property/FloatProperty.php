<?php

namespace GeometriaLab\Model\Schema\Property;

class FloatProperty extends AbstractProperty
{
    protected function setup()
    {
        $this->addTypeValidator('float');

        $this->addTypeFilter(function ($value) {
            $filteredValue = (float)$value;
            if ((string)$filteredValue === $value) {
                return $filteredValue;
            }
            return $value;
        });
    }
}