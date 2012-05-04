<?php

/**
 *
 */
class GeometriaLab_Model_Definition_Property_Model extends GeometriaLab_Model_Definition_Property_Abstract
{
    /**
     * @var GeometriaLab_Model_Definition
     */
    protected $_modelDefinition;

    public function setModelDefinition(GeometriaLab_Model_Definition $modelDefinition)
    {
        $this->_modelDefinition = $modelDefinition;

        return $this;
    }

    public function getModelDefinition()
    {
        return $this->_modelDefinition;
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