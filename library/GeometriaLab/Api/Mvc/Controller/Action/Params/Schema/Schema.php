<?php

namespace GeometriaLab\Api\Mvc\Controller\Action\Params\Schema;

use GeometriaLab\Model\Schema\Property\PropertyInterface;

class Schema extends \GeometriaLab\Model\Schema\Schema
{
    /**
     * Expected properties interface
     *
     * @var array
     */
    static protected $propertyInterface = 'GeometriaLab\Api\Mvc\Controller\Action\Params\Schema\Property\PropertyInterface';
}