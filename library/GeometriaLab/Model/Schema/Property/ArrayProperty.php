<?php

namespace GeometriaLab\Model\Schema\Property;

use GeometriaLab\Model\Schema\Schema;

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

    /**
     * Prepare value
     *
     * @todo Separate validation and filter
     * @param array $value
     * @return array
     * @throws \InvalidArgumentException
     */
    public function prepare($value)
    {
        if (!is_array($value)) {
            throw new \InvalidArgumentException("must be array");
        }

        if ($this->getItemProperty() !== null) {
            $value = array_map(array($this->getItemProperty(), 'prepare'), $value);
        }

        return $value;
    }
}