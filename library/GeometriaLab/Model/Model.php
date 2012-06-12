<?php

namespace GeometriaLab\Model;

use GeometriaLab\Model\Definition;

class Model extends Schemaless\Model implements ModelInterface
{
    /**
     * Model definition
     *
     * @var Definition|Definition\DefinitionInterface
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
     * Get property value
     *
     * @param $name
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function get($name)
    {
        $property = $this->getPropertyDefinition($name);
        if ($property === null) {
            throw new \InvalidArgumentException("Property '$name' does not exists");
        }

        $method = "get{$name}";
        if (method_exists($this, $method)) {
            return call_user_func(array($this, $method));
        }

        if (isset($this->propertyValues[$name])) {
            return $this->propertyValues[$name];
        } else {
            return null;
        }
    }

    /**
     * Set property value
     *
     * @param string $name
     * @param mixed $value
     * @return Model|ModelInterface
     * @throws \InvalidArgumentException
     */
    public function set($name, $value)
    {
        $property = $this->getPropertyDefinition($name);
        if ($property === null) {
            throw new \InvalidArgumentException("Property '$name' does not exists");
        }

        try {
            $value = $property->prepare($value);
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException("Invalid value for property '$name'");
        }

        $method = "set{$name}";
        if (method_exists($this, $method)) {
            return call_user_func(array($this, $method), $value);
        }

        $this->propertyValues[$name] = $value;

        return $this;
    }

    /**
     * Get definition
     *
     * @return Definition|Definition\DefinitionInterface
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * Create model definition
     *
     * @return Definition|Definition\DefinitionInterface
     */
    static public function createDefinition()
    {
        $definitions = Definition\Manager::getInstance();

        $className = get_called_class();

        if (!$definitions->has($className)) {
            $definitions->add(new Definition($className));
        }

        return $definitions->get($className);
    }

    /**
     * Setup model
     */
    protected function setup()
    {
        $this->definition = static::createDefinition();

        // Fill default values
        /**
         * @var Definition\Property\PropertyInterface $property
         */
        foreach($this->getProperties() as $name => $property) {
            if ($property->getDefaultValue() !== null) {
                $this->set($name, $property->getDefaultValue());
            }
        }
    }

    /**
     * Get properties
     *
     * @return array
     */
    protected function getProperties()
    {
        return $this->getDefinition()->getProperties();
    }

    /**
     * Get property definition
     *
     * @param string $name
     * @return Definition\Property\PropertyInterface|null
     */
    protected function getPropertyDefinition($name)
    {
        if ($this->getDefinition()->hasProperty($name)) {
            return $this->getDefinition()->getProperty($name);
        } else {
            return null;
        }
    }
}