<?php

namespace GeometriaLab\Api\Mvc\Controller\Action\Params\Schema\Property;

class IntegerProperty extends \GeometriaLab\Model\Schema\Property\IntegerProperty  implements PropertyInterface
{
    /**
     * Attach filter for casting value
     */
    public function setup()
    {
        $this->getFilterChain()->attach(function ($value) {
            $filteredValue = (int)$value;
            if ((string)$filteredValue == $value) {
                return $filteredValue;
            }
            return $value;
        }, 10000);
    }
}