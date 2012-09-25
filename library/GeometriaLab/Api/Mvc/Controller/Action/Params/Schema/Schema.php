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

    /**
     * Validate property
     *
     * @param PropertyInterface $property
     * @throws \RuntimeException
     */
    protected function validateProperty(PropertyInterface $property)
    {
        parent::validateProperty($property);

        if (is_a($property, '\GeometriaLab\Api\Mvc\Controller\Action\Params\Schema\Property\ArrayProperty')) {
            $itemProperty = $property->getItemProperty();
            if (is_a($itemProperty, '\GeometriaLab\Model\Schema\Property\ModelProperty')) {
                throw new \RuntimeException("Item of array property '{$property->getName()}' mustn't be an instance of \\GeometriaLab\\Model\\Schema\\Property\\ModelProperty");
            }
        }

    }
}