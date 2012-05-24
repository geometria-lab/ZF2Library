<?php

namespace GeometriaLab\Model\Definition\Property;

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
            $this->itemProperty = Factory::factory($this->getItemType());
        }

        return $this->itemProperty;
    }

    /**
     * Validate value
     *
     * @param mixin $value
     * @return bool
     */
    public function isValid($value)
    {
        if (!is_array($value)) {
            return false;
        }

        foreach($value as $item) {
            if (!$this->getItemProperty()->isValid($item)) {
                return false;
            }
        }

        return true;
    }
}