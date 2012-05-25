<?php

namespace GeometriaLab\Model;

use GeometriaLab\Model\Definition;

abstract class Model extends Schemaless
{
    /**
     * Model definition
     *
     * @var DefinitionInterface
     */
    protected static $definition;

    /**
     * @var string
     */
    protected static $definitionClass = '\GeometriaLab\Model\Definition';

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
     * @param $name
     * @param $value
     * @return Model
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
        } catch (InvalidArgumentException $e) {
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
     * Create model definition by class name
     *
     * @param string $className
     * @return \GeometriaLab\Model\Definition\DefinitionInterface
     */
    public static function getDefinition()
    {
        if (static::$definition === null) {
            static::$definition = new static::$definitionClass(get_called_class());
        }

        return static::$definition;
    }

    /**
     * Setup model
     */
    protected function setup()
    {
        $definitions = Definition\Manager::getInstance();

        if (!$definitions->has(get_class($this))) {
            $definitions->add(static::getDefinition());
        }

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
        return static::$definition->getProperties();
    }

    /**
     * Get property definition
     *
     * @param string $name
     * @return Definition\Property\PropertyInterface|null
     */
    protected function getPropertyDefinition($name)
    {
        if (static::$definition->hasProperty($name)) {
            return static::$definition->getProperty($name);
        } else {
            return null;
        }
    }
}