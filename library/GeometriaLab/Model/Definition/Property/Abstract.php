<?php

/**
 * @author Ivan Shumkov
 */
abstract class GeometriaLab_Model_Definition_Property_Abstract
{
    /**
     * Name
     *
     * @var string
     */
    protected $_name;

    /**
     * Model definition
     *
     * @var GeometriaLab_Model_Definition
     */
    protected $_modelDefinition;

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
     * Set model definition
     *
     * @param GeometriaLab_Model_Definition $modelDefinition
     */
    public function setModelDefinition(GeometriaLab_Model_Definition $modelDefinition)
    {
        $this->_modelDefinition = $modelDefinition;
    }

    /**
     * Get model definition
     *
     * @return GeometriaLab_Model_Definition
     */
    public function getModelDefinition()
    {
        return $this->_modelDefinition;
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
     * Has setter
     *
     * @return bool
     */
    public function hasSetter()
    {
        return $this->_hasMethod($this->_hasSetter, 'set');
    }

    /**
     * Has getter
     *
     * @return bool
     */
    public function hasGetter()
    {
        return $this->_hasMethod($this->_hasGetter, 'get');
    }

    public function isValid($value)
    {
        return $value === null || $this->_isValid($value);
    }

    abstract protected function _isValid($value);

    /**
     * Has getter or setter helper
     *
     * @param $variable
     * @param $methodPrefix
     * @return bool
     */
    protected function _hasMethod(&$variable, $methodPrefix)
    {
        if ($variable === null) {
            $className = $this->getModelDefinition()->getClassName();
            $method = $methodPrefix . $this->getName();
            $variable = method_exists($className, $method);
        }

        return $variable;
    }
}