<?php

namespace GeometriaLab\Api\Mvc\Controller\Action\Params\Schema;

use GeometriaLab\Model\Schema\Property\PropertyInterface;

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

    /**
     * Validate property
     *
     * @param PropertyInterface $property
     * @throws \RuntimeException
     */
    protected function validateProperty(PropertyInterface $property)
    {
        if (is_a($property, '\GeometriaLab\Model\Schema\Property\ModelProperty')) {
            throw new \RuntimeException("Property '{$property->getName()}' mustn't be an instance of \\GeometriaLab\\Model\\Schema\\Property\\ModelProperty");
        }

        parent::validateProperty($property);
    }
}