<?php

namespace GeometriaLab\Api\Mvc\Controller\Action\Params\Schema\Property;

class FloatProperty extends \GeometriaLab\Model\Schema\Property\FloatProperty implements PropertyInterface
{
    /**
     * Attach filter for casting value
     */
    public function setup()
    {
        $this->getFilterChain()->attach(function ($value) {
            $filteredValue = (float)$value;
            if ((string)$filteredValue == $value) {
                return $filteredValue;
            }
            return $value;
        }, 10000);
    }
}