<?php

namespace GeometriaLab\Model\Schema\Property;

class StringProperty extends AbstractProperty
{
    protected function setup()
    {
        $this->addTypeValidator('string');

        $this->addTypeFilter(function ($value) {
            return (string)$value;
        });
    }
}