<?php

namespace GeometriaLab\Model\Schema\Property;

class FloatProperty extends AbstractProperty
{
    protected function setup()
    {
        $this->addTypeValidator('float');

        $this->getFilterChain()->attach(function ($value) {
            $filteredValue = (float)$value;
            if ((string)$filteredValue === $value) {
                return $filteredValue;
            }
            return $value;
        }, 10000);
    }
}