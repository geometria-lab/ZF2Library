<?php

namespace GeometriaLab\Api\Mvc\Controller\Action\Params\Schema\Property;

use GeometriaLab\Model\Schema\Property\PropertyInterface as SchemaPropertyInterface;

class ArrayProperty extends \GeometriaLab\Model\Schema\Property\ArrayProperty implements PropertyInterface
{
    /**
     * @param SchemaPropertyInterface $property
     * @return ArrayProperty
     * @throws \RuntimeException
     */
    public function setItemProperty(SchemaPropertyInterface $property)
    {
        if (!$property instanceof PropertyInterface) {
            throw new \RuntimeException("Item of array property must be an instance of \\GeometriaLab\\Api\\Mvc\\Controller\\Action\\Params\\Schema\\Property\\PropertyInterface");
        }

        return parent::setItemProperty($property);
    }
}