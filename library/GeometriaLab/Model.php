<?php

/**
 * @author Ivan Shumkov, munkie, vin
 */
abstract class GeometriaLab_Model implements Iterator
{
    /**
     * Definitions
     *
     * @var GeometriaLab_Model_Definition_Manager
     */
    static protected $_definitions;

    /**
      * Property iterator
      *
      * @var array
      */
    protected $_propertyIteratorPosition = null;

    /**
     * Property values
     *
     * @var array
     */
    protected $_propertyValues = array();

    /**
     * Class name
     *
     * @var string
     */
    protected $_className;

    /**
     * Is iterable validator
     *
     * @var GeometriaLab_Validate_IsIterable
     */
    static protected $_isIterableValidator;

    /**
     * Constructor
     *
     * @param mixed $data Model data (must be array or iterable object)
     */
    public function __construct($data = null)
    {
        $this->_setup();

        if ($data !== null) {
            $this->populate($data);
        }

        $this->init();
    }

    /**
     * Populate model from array or iterable object
     *
     * @param array|Traversable $data                      Model data (must be array or iterable object)
     * @param bool              $ignoreUndefinedProperties
     * @return GeometriaLab_Model
     * @throws GeometriaLab_Model_Exception
     */
    public function populate($data, $ignoreUndefinedProperties = true)
    {
        if (!self::_isIterable($data)) {
            throw new GeometriaLab_Model_Exception("Can't populate data. Must be array or iterated object.");
        }

        foreach ($data as $key => $value) {
            if ($this->hasProperty($key)) {
                $this->setProperty($key, $value);
            } else if (!$ignoreUndefinedProperties) {
                throw new GeometriaLab_Model_Exception("Trying to set property $key, that not exists in object $this->_className");
            }
        }

        return $this;
    }

    /**
     * Get property
     *
     * @param $name
     * @return mixed
     * @throws GeometriaLab_Model_Exception
     */
    public function getProperty($name)
    {
        $property = $this->_getPropertyDefinition($name);
        if ($property === null) {
            throw new GeometriaLab_Model_Exception("Property '$name' does not exists");
        }

        $method = "get{$name}";
        if (method_exists($this, $method)) {
            return call_user_func(array($this, $method));
        }

        return $this->_propertyValues[$name];
    }

    /**
     * Magic for get property
     *
     * @param $name
     * @return mixed
     * @throws GeometriaLab_Model_Exception
     */
    public function __get($name)
    {
        return $this->getProperty($name);
    }

    /**
     * Set property
     *
     * @param $name
     * @param $value
     * @return GeometriaLab_Model
     * @throws GeometriaLab_Model_Exception
     */
    public function setProperty($name, $value)
    {
        $property = $this->_getPropertyDefinition($name);
        if ($property === null) {
            throw new GeometriaLab_Model_Exception("Property '$name' does not exists");
        }
        if (!$property->isValid($value)) {
            throw new GeometriaLab_Model_Exception("Invalid value for property '$name'");
        }

        $method = "set{$name}";
        if (method_exists($this, $method)) {
            return call_user_func(array($this, $method), $value);
        }

        $this->_propertyValues[$name] = $value;

        return $this;
    }

    /**
     * Magic for set property
     *
     * @param $name
     * @param $value
     * @return GeometriaLab_Model
     * @throws GeometriaLab_Model_Exception
     */
    public function __set($name, $value)
    {
        return $this->setProperty($name, $value);
    }

    /**
     * Has property
     *
     * @param $name
     * @return bool
     */
    public function hasProperty($name)
    {
        return $this->_getPropertyDefinition($name) !== null;
    }

    /**
     * Magic for get property
     *
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return $this->hasProperty($name);
    }

    /**
     * Convert model to array
     *
     * @return array
     */
    public function toArray()
    {
        $array = array();

        foreach ($this->_getProperties() as $name => $property) {
            $array[$name] = $this->getProperty($name);
        }

        return $array;
    }

    /**
     * Get class name
     *
     * @return string
     */
    protected function _getClassName()
    {
        if ($this->_className === null) {
            $this->_className = get_class($this);
        }

        return $this->_className;
    }

    /**
     * Get properties
     *
     * @return array
     */
    protected function _getProperties()
    {
        $className = $this->_getClassName();
        return self::$_definitions->get($className)->getProperties();
    }

    /**
     * Setup model
     */
    protected function _setup()
    {
        $className = $this->_getClassName();

        self::$_definitions = GeometriaLab_Model_Definition_Manager::getInstance();

        if (!self::$_definitions->has($className)) {
            self::$_definitions->define($className);
        }

        foreach($this->_getProperties() as $name => $property) {
            $this->setProperty($name, $property->getDefaultValue());
        }
    }

    /**
     * Get property definition
     *
     * @param string $name
     * @return GeometriaLab_Model_Definition_Property|null
     */
    protected function _getPropertyDefinition($name)
    {
        $className = $this->_getClassName();

        if (self::$_definitions->get($className)->hasProperty($name)) {
            return self::$_definitions->get($className)->getProperty($name);
        } else {
            return null;
        }
    }

    /**
     * Is iterable
     *
     * @static
     * @param $data
     * @return bool
     */
    static protected function _isIterable($data)
    {
        if (self::$_isIterableValidator === null) {
            self::$_isIterableValidator = new GeometriaLab_Validate_IsIterable();
        }

        return self::$_isIterableValidator->isValid($data);
    }

    /*
     * Methods implements Iterator
     */

    public function current()
    {
        $name = $this->key();

        return $this->getProperty($name);
    }

    public function next()
    {
        return next($this->_propertyIteratorPosition);
    }

    public function key()
    {
        return current($this->_propertyIteratorPosition);
    }

    public function valid()
    {
        return key($this->_propertyIteratorPosition) !== null;
    }

    public function rewind()
    {
        if ($this->_propertyIteratorPosition === null) {
            $this->_propertyIteratorPosition = array_keys($this->_getProperties());
        }

        return reset($this->_propertyIteratorPosition);
    }

    /**
     * Callbacks
     */

    public function init()
    {

    }
}