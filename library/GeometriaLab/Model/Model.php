<?php

namespace GeometriaLab\Model;

use GeometriaLab\Model\Schema;

/**
 *
 */
class Model extends Schemaless\Model implements ModelInterface
{
    /**
     * Model schema
     *
     * @var Schema
     */
    protected $schema;

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
        if (!$this->getSchema()->hasProperty($name)) {
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
        if (!$this->getSchema()->hasProperty($name)) {
            throw new \InvalidArgumentException("Property '$name' does not exists");
        }

        $property = $this->getSchema()->getProperty($name);

        try {
            $value = $property->prepare($value);
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException("Invalid value for property '$name': " . $e->getMessage());
        }

        $method = "set{$name}";
        if (method_exists($this, $method)) {
            return call_user_func(array($this, $method), $value);
        }

        $this->propertyValues[$name] = $value;

        return $this;
    }

    /**
     * Get schema
     *
     * @return Schema
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * Set schema
     *
     * @param Schema $schema
     * @return Model
     */
    public function setSchema(Schema $schema)
    {
        $this->schema = $schema;

        return $this;
    }

    /**
     * Create model schema
     *
     * @return Schema
     */
    static public function createSchema()
    {
        $schemas = Schema\Manager::getInstance();

        $className = get_called_class();

        if (!$schemas->has($className)) {
            $schemas->add(new Schema($className));
        }

        return $schemas->get($className);
    }

    /**
     * Setup model
     */
    protected function setup()
    {
        $this->schema = static::createSchema();

        // Fill default values
        /**
         * @var Schema\Property\PropertyInterface $property
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
     * @return Schema\Property\PropertyInterface[]
     */
    protected function getProperties()
    {
        return $this->getSchema()->getProperties();
    }
}