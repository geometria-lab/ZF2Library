<?php

namespace GeometriaLab\Model\Schema\Property;

class IntegerProperty extends AbstractProperty
{
    protected function setup()
    {
        $this->addTypeValidator('integer');

        $this->getFilterChain()->attach(function ($value) {
            $filteredValue = (int)$value;
            if ((string)$filteredValue === $value) {
                return $filteredValue;
            }
            return $value;
        }, 10000);
    }
}