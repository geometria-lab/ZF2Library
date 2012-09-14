<?php

namespace GeometriaLab\Model\Schema\Property;

class FloatProperty extends AbstractProperty
{
    protected function setup()
    {
        $this->addTypeValidator('float');
    }
}