<?php

namespace GeometriaLab\Model\Schema\Property;

class IntegerProperty extends AbstractProperty
{
    protected function setup()
    {
        $this->addTypeValidator('integer');
    }
}