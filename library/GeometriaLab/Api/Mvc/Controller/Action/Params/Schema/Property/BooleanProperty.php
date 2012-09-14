<?php

namespace GeometriaLab\Api\Mvc\Controller\Action\Params\Schema\Property;

class BooleanProperty extends \GeometriaLab\Model\Schema\Property\BooleanProperty implements PropertyInterface
{
    /**
     * Attach filter for casting value
     */
    public function setup()
    {
        $this->getFilterChain()->attach(function ($value) {
            if ($value === 'true' || $value === '1') {
                return true;
            } elseif ($value === 'false' || $value === '0') {
                return false;
            } else {
                return $value;
            }
        }, 10000);
    }
}