<?php

/**
 * @author Ivan Shumkov
 */
abstract class GeometriaLab_Model_Definition_Property_Abstract implements GeometriaLab_Model_Definition_Property_Interface
{
    /**
     * Name
     *
     * @var string
     */
    protected $_name;

    /**
     * Default value
     *
     * @var mixin
     */
    protected $_defaultValue;

    /**
     * Has getter
     *
     * @var null|boolean
     */
    protected $_hasGetter;

    /**
     * Has setter
     *
     * @var null|boolean
     */
    protected $_hasSetter;

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Set name
     *
     * @param $name
     * @return GeometriaLab_Model_Definition_Property_Abstract
     */
    public function setName($name)
    {
        $this->_name = $name;

        return $this;
    }

    /**
     * Get default value
     *
     * @return mixin
     */
    public function getDefaultValue()
    {
        return $this->_defaultValue;
    }

    /**
     * Set default value
     *
     * @return GeometriaLab_Model_Definition_Property_Abstract
     */
    public function setDefaultValue($value)
    {
        $this->_defaultValue = $value;

        return $this;
    }

    /**
     * Validate property value
     *
     * @param mixed $value
     * @return bool
     */
    public function isValid($value)
    {
        return $value === null || $this->_isValid($value);
    }

    abstract protected function _isValid($value);
}