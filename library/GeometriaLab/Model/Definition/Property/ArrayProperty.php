<?php

namespace GeometriaLab\Model\Definition\Property;

use GeometriaLab\Model\Definition;

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
        if ($this->itemProperty === null) {
            $this->itemProperty = Definition::createProperty($this->getItemType());
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
            throw new \InvalidArgumentException();
        }

        $value = array_map(array($this->getItemProperty(), 'prepare'), $value);

        return $value;
    }
}