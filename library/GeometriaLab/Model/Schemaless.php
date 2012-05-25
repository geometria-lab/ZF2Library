<?php

namespace GeometriaLab\Model;

class Schemaless implements ModelInterface, \Iterator, \Countable
{
    /**
     * Property iterator
     *
     * @var array|null
     */
    protected $propertyIteratorPosition;

    /**
     * Property values
     *
     * @var array
     */
    protected $propertyValues = array();

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
    }

    /**
     * Populate model from array or iterable object
     *
     * @param array|\Traversable|\stdClass $data  Model data (must be array or iterable object)
     * @return \GeometriaLab\Model\Schemaless
     * @throws \InvalidArgumentException
     */
    public function populate($data)
    {
        if (!is_array($data) && !$data instanceof \Traversable && !$data instanceof \stdClass) {
            throw new \InvalidArgumentException("Can't populate data. Must be array or iterated object.");
        }

        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * Get property value
     *
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        if (isset($this->propertyValues[$name])) {
            return $this->propertyValues[$name];
        } else {
            return null;
        }
    }

    /**
     * Magic get property value
     *
     * @param $name
     * @return mixed
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
     * @return \GeometriaLab\Model\Schemaless
     */
    public function set($name, $value)
    {
        $this->propertyValues[$name] = $value;

        return $this;
    }

    /**
     * Magic for set property
     *
     * @param $name
     * @param $value
     * @return \GeometriaLab\Model\Schemaless
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
        return isset($this->propertyValues[$name]);
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

        foreach ($this->getProperties() as $name => $property) {
            $array[$name] = $this->get($name);
        }

        return $array;
    }

    /**
     * Get properties
     *
     * @return array
     */
    protected function getProperties()
    {
        return $this->propertyValues;
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
        return next($this->propertyIteratorPosition);
    }

    public function key()
    {
        return current($this->propertyIteratorPosition);
    }

    public function valid()
    {
        return key($this->propertyIteratorPosition) !== null;
    }

    public function rewind()
    {
        if ($this->propertyIteratorPosition === null) {
            $this->propertyIteratorPosition = array_keys($this->getProperties());
        }

        return reset($this->propertyIteratorPosition);
    }

    /*
    * Methods implements Iterator
    */

    public function count()
    {
        return count($this->getProperties());
    }
}