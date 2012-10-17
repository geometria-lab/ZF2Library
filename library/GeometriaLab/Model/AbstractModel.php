<?php

namespace GeometriaLab\Model;

use GeometriaLab\Model\Schema\SchemaInterface,
    GeometriaLab\Model\Schema\Property\PropertyInterface,
    GeometriaLab\Model\Schema\Manager as SchemaManager;

abstract class AbstractModel implements ModelInterface
{
    /**
     * Property values
     *
     * @var array
     */
    protected $propertyValues = array();
    /**
     * Parser class name
     *
     * @var string
     */
    static protected $parserClassName = 'GeometriaLab\Model\Schema\DocBlockParser';
    /**
     * Validation error messages
     *
     * @var array
     */
    protected $errorMessages = array();

    /**
     * Constructor
     *
     * @param mixed $data Model data (must be array or iterable object)
     */
    public function __construct($data = null)
    {
        $this->setup();

        if ($data !== null) {
            $this->populate($data);
        }
    }

    /**
     * Populate model from array or iterable object
     *
     * @param array|\Traversable|\stdClass $data  Model data (must be array or iterable object)
     * @param bool $notValidate
     * @return AbstractModel
     * @throws \InvalidArgumentException
     */
    public function populate($data, $notValidate = false)
    {
        if (!is_array($data) && !$data instanceof \Traversable && !$data instanceof \stdClass) {
            throw new \InvalidArgumentException("Can't populate data. Must be array or iterated object.");
        }

        foreach ($data as $key => $value) {
            $this->set($key, $value, $notValidate);
        }

        return $this;
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
        if (!static::getSchema()->hasProperty($name)) {
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
     * Set property value
     *
     * @param string $name
     * @param mixed $value
     * @param bool $notValidate
     * @return AbstractModel|ModelInterface
     * @throws \InvalidArgumentException
     */
    public function set($name, $value, $notValidate = false)
    {
        if ($value !== null) {
            $property = static::getSchema()->getProperty($name);

            if ($this->$name === $value) {
                return $this;
            }
            if ($notValidate) {
                $value = $property->getFilterChain()->filter($value);
            } else {
                $value = $property->filterAndValidate($value);
            }
        }

        $method = "set{$name}";
        if (method_exists($this, $method)) {
            call_user_func(array($this, $method), $value);
        } else {
            $this->propertyValues[$name] = $value;
        }

        return $this;
    }

    /**
     * Magic for set property
     *
     * @param string $name
     * @param mixed $value
     * @return AbstractModel
     */
    public function __set($name, $value)
    {
        return $this->set($name, $value);
    }

    /**
     * Has property?
     *
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return static::getSchema()->hasProperty($name);
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
     * IteratorAggregate implementation
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->toArray());
    }

    /**
     * Is Valid model data
     *
     * @return bool
     */
    public function isValid()
    {
        foreach ($this->getProperties() as $property) {
            if ($property->isRequired()) {
                $name = $property->getName();
                $value = $this->get($name);

                if ($value === null) {
                    $this->errorMessages[$name] = array();
                    $this->errorMessages[$name]['isRequired'] = "Value is required";
                }
            }
        }

        return empty($this->errorMessages);
    }

    /**
     * Get validation error messages (after isValid called)
     *
     * @return array
     */
    public function getErrorMessages()
    {
        return $this->errorMessages;
    }

    /**
     * Get schema
     *
     * @return SchemaInterface
     */
    static public function getSchema()
    {
        $schemaManager = SchemaManager::getInstance();

        $className = get_called_class();

        if (!$schemaManager->has($className)) {
            $parserClassName = static::$parserClassName;
            $schema = $parserClassName::getInstance()->createSchema($className);
            $schemaManager->add($schema);
        }

        return $schemaManager->get($className);
    }

    /**
     * Setup model
     */
    protected function setup()
    {
        // Fill default values
        /**
         * @var PropertyInterface $property
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
     * @return PropertyInterface[]
     */
    protected function getProperties()
    {
        return static::getSchema()->getProperties();
    }
}