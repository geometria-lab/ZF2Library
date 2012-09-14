<?php

namespace GeometriaLab\Model\Schema\Property;

class BooleanProperty extends AbstractProperty
{
    protected function setup()
    {
        $this->addTypeValidator('boolean');

        $this->addTypeFilter(function($value) {
            if ($value === 'true' || $value === '1') {
                return true;
            } elseif ($value === 'false' || $value === '0') {
                return false;
            } else {
                return $value;
            }
        });
    }
}