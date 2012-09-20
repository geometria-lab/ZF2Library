<?php

namespace GeometriaLab\Api\Mvc\Controller\Action\Params\Schema;

class Schema extends \GeometriaLab\Model\Schema\Schema
{
    /**
     * Expected properties namespaces
     *
     * @var array
     */
    static protected $propertyNamespaces = array(
        'GeometriaLab\Model\Persistent\Schema\Property',
    );
}