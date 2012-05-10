<?php

/**
 * @author Ivan Shumkov
 */
class GeometriaLab_Model_Schemaless implements Iterator, Countable
{
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
     * Constructor
     *
     * @param mixed $data Model data (must be array or iterable object)
     */
    public function __construct($data = null)
    {
        if ($data !== null) {
            $this->populate($data);
        }

        $this->init();
    }

    /**
     * Populate model from array or iterable object
     *
     * @param array|Traversable $data  Model data (must be array or iterable object)
     * @return GeometriaLab_Model
     * @throws GeometriaLab_Model_Exception
     */
    public function populate($data)
    {
        if (!GeometriaLab_Validate_IsIterable::staticIsValid($data)) {
            throw new GeometriaLab_Model_Exception("Can't populate data. Must be array or iterated object.");
        }

        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * Get property
     *
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        if (isset($this->_propertyValues[$name])) {
            return $this->_propertyValues[$name];
        } else {
            return null;
        }
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
        return $this->get($name);
    }

    /**
     * Set property
     *
     * @param $name
     * @param $value
     * @return GeometriaLab_Model
     */
    public function set($name, $value)
    {
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
        return $this->set($name, $value);
    }

    /**
     * Has property
     *
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->_propertyValues[$name]);
    }

    /**
     * Magic for get property
     *
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return $this->has($name);
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
            $array[$name] = $this->get($name);
        }

        return $array;
    }

    /**
     * Get properties
     *
     * @return array
     */
    protected function _getProperties()
    {
        return $this->_propertyValues;
    }

    /*
     * Methods implements Iterator
     */

    public function current()
    {
        $name = $this->key();

        return $this->get($name);
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

    /*
    * Methods implements Iterator
    */

    public function count()
    {
        return count($this->_getProperties());
    }

    /**
     * Callbacks
     */

    public function init()
    {

    }
}