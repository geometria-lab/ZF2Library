<?php

namespace GeometriaLab\Model\Schemaless;

class Model implements ModelInterface
{
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
     * @return Model
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
     * @param string $name
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
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * Set property
     *
     * @param string $name
     * @param mixed $value
     * @return Model
     */
    public function set($name, $value)
    {
        $this->propertyValues[$name] = $value;

        return $this;
    }

    /**
     * Magic for set property
     *
     * @param string $name
     * @param mixed $value
     * @return Model
     */
    public function __set($name, $value)
    {
        return $this->set($name, $value);
    }

    /**
     * Has property
     *
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->propertyValues);
    }

    /**
     * Magic for get property
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return $this->has($name);
    }

    /**
     * Convert model to array
     *
     * @param integer $depth
     * @return array
     */
    public function toArray($depth = 0)
    {
        $array = array();
        foreach ($this->getProperties() as $name => $property) {
            $array[$name] = $this->get($name);

            if ($depth !== 0 && $array[$name] instanceof ModelInterface) {
                $array[$name] = $array[$name]->toArray($depth === -1 ? -1 : $depth - 1);
            }
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
}