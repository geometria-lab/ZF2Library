<?php

namespace GeometriaLab\Api\Mvc\Controller\Action\Params\Schema\Property;

class ArrayProperty extends \GeometriaLab\Model\Schema\Property\ArrayProperty implements PropertyInterface
{
    /**
     * Attach filter for casting value
     */
    public function setup()
    {
        parent::setup();
    }
}