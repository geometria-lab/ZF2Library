<?php

namespace GeometriaLab\Api\Mvc\Controller\Action\Params\Schema\Property;

interface PropertyInterface extends \GeometriaLab\Model\Schema\Property\PropertyInterface
{
    /**
     * Attach filter for casting value
     */
    public function setUp();
}