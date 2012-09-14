<?php

namespace GeometriaLab\Api\Mvc\Controller\Action\Params\Schema\Property;

class ArrayProperty extends \GeometriaLab\Model\Schema\Property\ArrayProperty implements PropertyInterface
{
    /**
     * Attach filter for casting value
     */
    public function setup()
    {
        $this->getFilterChain()->attach(function ($value) {
            if ($value[0] == '[' && $value[strlen($value) - 1] == ']') {
                $array = array();
                $value = substr($value, 1, -1);

                if ($value !== '') {
                    $array = array_map('trim', explode(',', $value));
                }

                return $array;
            }

            return $value;
        }, 10000);
    }
}