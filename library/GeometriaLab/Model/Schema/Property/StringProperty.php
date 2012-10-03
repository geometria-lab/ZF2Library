<?php

namespace GeometriaLab\Model\Schema\Property;

class StringProperty extends AbstractProperty
{
    protected function setup()
    {
        $type = 'string';
        $this->addTypeValidator($type);
        $this->addNotEmptyValidator($type);
    }
}