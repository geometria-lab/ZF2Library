<?php

namespace GeometriaLab\Api\Mvc\Controller\Action\Params\Schema\Property;

class StringProperty extends \GeometriaLab\Model\Schema\Property\StringProperty implements PropertyInterface
{
    /**
     * Attach filter for casting value
     */
    public function setup()
    {
        $this->getFilterChain()->attach(function ($value) {
            return (string)$value;
        }, 10000);
    }
}