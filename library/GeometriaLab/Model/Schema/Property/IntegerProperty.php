<?php

namespace GeometriaLab\Model\Schema\Property;

class IntegerProperty extends AbstractProperty
{
    protected function setup()
    {
        $type = 'integer';
        $this->addTypeValidator($type);
        $this->addNotEmptyValidator($type);

        $this->getFilterChain()->attach(function ($value) {
            $filteredValue = (int)$value;
            if ((string)$filteredValue === $value) {
                return $filteredValue;
            }
            return $value;
        }, 10000);
    }
}