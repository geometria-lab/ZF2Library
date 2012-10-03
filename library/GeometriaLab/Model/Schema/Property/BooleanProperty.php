<?php

namespace GeometriaLab\Model\Schema\Property;

class BooleanProperty extends AbstractProperty
{
    protected function setup()
    {
        $this->addTypeValidator('boolean');

        $this->getFilterChain()->attach(function($value) {
            switch (gettype($value)) {
                case 'boolean':
                    return $value;
                case 'integer':
                    if ($value === 1) {
                        return true;
                    } elseif ($value === 0) {
                        return false;
                    } else {
                        return $value;
                    }
                case 'string':
                    if ($value === 'true') {
                        return true;
                    } elseif ($value === 'false') {
                        return false;
                    } else {
                        return $value;
                    }
                default:
                    return $value;
            }
        }, 10000);
    }
}