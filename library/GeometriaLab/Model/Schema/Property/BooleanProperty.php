<?php

namespace GeometriaLab\Model\Schema\Property;

class BooleanProperty extends AbstractProperty
{
    protected function setup()
    {
        $this->addTypeValidator('boolean');
    }
}