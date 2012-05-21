<?php

namespace GeometriaLab\Model\Definition\Property;

class ArrayProperty extends AbstractProperty
{
    /**
     * @var PropertyInterface
     */
    protected $itemProperty;

    /**
     * Set item property object
     *
     * @param PropertyInterface $itemProperty
     * @return ArrayProperty
     */
    public function setItemProperty(PropertyInterface $itemProperty)
    {
        $this->_itemProperty = $itemProperty;

        return $this;
    }

    /**
     * Get item property object
     *
     * @return PropertyInterface
     */
    public function getItemProperty()
    {
        return $this->_itemProperty;
    }

    public function isValid($value)
    {
        if (!is_array($value)) {
            return false;
        }

        foreach($value as $item) {
            if (!$this->itemProperty->isValid($item)) {
                return false;
            }
        }

        return true;
    }
}