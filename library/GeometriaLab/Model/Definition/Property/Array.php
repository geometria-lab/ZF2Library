<?php

/**
 *
 */
class GeometriaLab_Model_Definition_Property_Array extends GeometriaLab_Model_Definition_Property_Abstract
{
    /**
     * @var GeometriaLab_Model_Definition_Property_Interface
     */
    protected $_itemProperty;

    /**
     * Set item property object
     *
     * @param GeometriaLab_Model_Definition_Property_Interface $itemProperty
     * @return GeometriaLab_Model_Definition_Property_Array
     */
    public function setItemProperty(GeometriaLab_Model_Definition_Property_Interface $itemProperty)
    {
        $this->_itemProperty = $itemProperty;

        return $this;
    }

    /**
     * Get item property object
     *
     * @return GeometriaLab_Model_Definition_Property_Interface
     */
    public function getItemProperty()
    {
        return $this->_itemProperty;
    }

    protected function _isValid($value)
    {
        if (!is_array($value)) {
            return false;
        }

        foreach($value as $item) {
            if (!$this->_itemProperty->isValid($item)) {
                return false;
            }
        }

        return true;
    }
}