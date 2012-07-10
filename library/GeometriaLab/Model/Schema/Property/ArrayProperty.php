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
     * @var string
     */
    protected $itemType;

    /**
     * Set item type
     *
     * @param string $type
     * @return ArrayProperty
     */
    public function setItemType($type)
    {
        $this->itemType = $type;

        return $this;
    }

    /**
     * Get item type
     *
     * @return string
     */
    public function getItemType()
    {
        return $this->itemType;
    }

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
        if ($this->itemProperty === null && $this->getItemType() !== null) {
            $this->itemProperty = Schema::createProperty($this->getItemType());
        }

        return $this->itemProperty;
    }

    /**
     * Prepare value
     *
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