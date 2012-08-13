<?php

namespace GeometriaLab\Model;

use GeometriaLab\Model\Schema\SchemaInterface,
    GeometriaLab\Model\Schema\Property\PropertyInterface,
    GeometriaLab\Model\Schema\Manager as SchemaManager;

/**
 * @todo Abstract?
 */
class Model extends Schemaless\Model implements ModelInterface
{
    static protected $schemaClassName = 'GeometriaLab\Model\Schema\Schema';

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
     * Set property value
     *
     * @param string $name
     * @param mixed $value
     * @return Model|ModelInterface
     * @throws \InvalidArgumentException
     */
    public function set($name, $value)
    {
        $schema = static::getSchema();

        if (!$schema->hasProperty($name)) {
            throw new \InvalidArgumentException("Property '$name' does not exists");
        }

        if ($value !== null) {
            $property = $schema->getProperty($name);

            try {
                $value = $property->prepare($value);
            } catch (\InvalidArgumentException $e) {
                throw new \InvalidArgumentException("Invalid value for property '$name': " . $e->getMessage());
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
     * Get schema
     *
     * @return SchemaInterface
     */
    static public function getSchema()
    {
        $schemas = SchemaManager::getInstance();

        $className = get_called_class();

        if (!$schemas->has($className)) {
            $schemas->add(new static::$schemaClassName($className));
        }

        return $schemas->get($className);
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