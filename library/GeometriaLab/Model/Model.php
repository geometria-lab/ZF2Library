<?php

namespace GeometriaLab\Model;

abstract class Model extends Schemaless
{
    /**
     * Model definition
     *
     * @var Definition
     */
    protected $definition;

    /**
     * Constructor
     *
     * @param mixed $data Model data (must be array or iterable object)
     */
    public function __construct($data = null)
    {
        $this->setup();

        parent::__construct($data);
    }

    /**
     * Populate model from array or iterable object
     *
     * @param array|\Traversable $data                      Model data (must be array or iterable object)
     * @param bool               $ignoreUndefinedProperties
     * @return Model
     * @throws \Exception
     */
    public function populate($data, $ignoreUndefinedProperties = false)
    {
        if (!is_array($data) && !$data instanceof \Traversable) {
            throw new \Exception("Can't populate data. Must be array or iterated object.");
        }

        foreach ($data as $key => $value) {
            if ($this->getPropertyDefinition($key) !== null) {
                $this->set($key, $value);
            } else if (!$ignoreUndefinedProperties) {
                throw new \Exception("Property '$key' not defined");
            }
        }

        return $this;
    }

    /**
     * Get property value
     *
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function get($name)
    {
        $property = $this->getPropertyDefinition($name);
        if ($property === null) {
            throw new \Exception("Property '$name' does not exists");
        }

        $method = "get{$name}";
        if (method_exists($this, $method)) {
            return call_user_func(array($this, $method));
        }

        return $this->propertyValues[$name];
    }

    /**
     * Set property value
     *
     * @param $name
     * @param $value
     * @return Model
     * @throws \Exception
     */
    public function set($name, $value)
    {
        $property = $this->getPropertyDefinition($name);
        if ($property === null) {
            throw new \Exception("Property '$name' does not exists");
        }
        if (!$property->isValid($value)) {
            throw new \Exception("Invalid value for property '$name'");
        }

        $method = "set{$name}";
        if (method_exists($this, $method)) {
            return call_user_func(array($this, $method), $value);
        }

        $this->propertyValues[$name] = $value;

        return $this;
    }

    /**
     * Setup model
     */
    protected function setup()
    {
        $className = get_class();

        $definitions = Definition\Manager::getInstance();

        if (!$definitions->has($className)) {
            $definitions->define($className);
        }

        $this->definition = $definitions->get($className);

        // Fill default values
        /**
         * @var Definition\Property\PropertyInterface $property
         */
        foreach($this->getProperties() as $name => $property) {
            $this->set($name, $property->getDefaultValue());
        }
    }

    /**
     * Get properties
     *
     * @return array
     */
    protected function getProperties()
    {
        return $this->definition->getProperties();
    }

    /**
     * Get property definition
     *
     * @param string $name
     * @return Definition\Property\PropertyInterface|null
     */
    protected function getPropertyDefinition($name)
    {
        if ($this->definition->hasProperty($name)) {
            return $this->definition->getProperty($name);
        } else {
            return null;
        }
    }
}