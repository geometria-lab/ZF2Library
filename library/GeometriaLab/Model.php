<?php

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
    protected $_propertyIterator = null;

    /**
     * Property values
     *
     * @var array
     */
    protected $_propertyValues = array();


    public function __construct($data = null)
    {
        $this->_setup();

        if ($data !== null) {
            $this->populate($data);
        }

        $this->init();
    }

    public function populate($data, $throwExceptions = false)
    {
        if ((is_object($data) && !($data instanceof Traversable)) || !is_array($data)) {
            throw new GeometriaLab_Model_Exception("Can't populate data. Must be array or iterated object.");
        }

        foreach ($data as $key => $value) {
            if ($this->hasProperty($key)) {
                $this->setProperty($key, $value);
            } else if ($throwExceptions) {
                throw new GeometriaLab_Model_Exception("Trying to set property $key, that not exists in object $this->_className");
            }
        }

        return $this;
    }

    public function init()
    {

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

        if ($property->hasGetter()) {
            return call_user_func(array($this, "get{$name}"));
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

        if ($property->hasSetter()) {
            return call_user_func(array($this, "set{$name}"), $value);
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

    protected function _getClassName()
    {
        return get_class($this);
    }

    protected function _setup()
    {
        $className = $this->_getClassName();

        self::$_definitions = GeometriaLab_Model_Definition_Manager::getInstance();

        if (!self::$_definitions->has($className)) {
            self::$_definitions->add($className);
        }

        foreach(self::$_definitions->get($className)->getProperties() as $name => $property) {
            $this->_propertyValues[$name] = $property->getDefaultValue();
        }
    }

    protected function _getPropertyDefinition($name)
    {
        $className = $this->_getClassName();

        if (self::$_definitions->get($className)->hasProperty($name)) {
            return self::$_definitions->get($className)->getProperty($name);
        } else {
            return null;
        }
    }

    /*
     * Implements Iterator
     */

    public function current()
    {
        $name = $this->key();

        return $this->getProperty($name);
    }

    public function next()
    {
        return next($this->_propertyIterator);
    }

    public function key()
    {
        return current($this->_propertyIterator);
    }

    public function valid()
    {
        return key($this->_propertyIterator) !== null;
    }

    public function rewind()
    {
        if ($this->_propertyIterator === null) {
            $className = $this->_getClassName();
            $this->_propertyIterator = array_keys(self::$_definitions->get($className)
                                                                     ->getProperties());
        }

        return reset($this->_propertyIterator);
    }
}