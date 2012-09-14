<?php

namespace GeometriaLab\Model\Schema\Property;

class ArrayProperty extends AbstractProperty
{
    /**
     * @var PropertyInterface
     */
    protected $itemProperty;

    /**
     * Set item property
     *
     * @param PropertyInterface $property
     * @return ArrayProperty
     */
    public function setItemProperty(PropertyInterface $property)
    {
        $this->itemProperty = $property;

        return $this;
    }

    /**
     * Get item property
     *
     * @return PropertyInterface
     */
    public function getItemProperty()
    {
        return $this->itemProperty;
    }

    protected function setup()
    {
        $validator = new Validator\ArrayItem($this);
        $this->getValidatorChain()->addValidator($validator);
    }
}