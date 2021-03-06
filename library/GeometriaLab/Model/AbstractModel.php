<?php

namespace GeometriaLab\Model;

use GeometriaLab\Model\Schema\SchemaInterface,
    GeometriaLab\Model\Schema\Property\PropertyInterface,
    GeometriaLab\Model\Schema\Manager as SchemaManager;

abstract class AbstractModel extends Schemaless\Model implements ModelInterface
{
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

        return parent::get($name);
    }

    /**
     * Populate model from array or iterable object and doesn't validate it
     *
     * @param array|\Traversable|\stdClass $data  Model data (must be array or iterable object)
     * @return AbstractModel
     * @throws \InvalidArgumentException
     */
    public function populateWithoutValidation($data)
    {
        if (!is_array($data) && !$data instanceof \Traversable && !$data instanceof \stdClass) {
            throw new \InvalidArgumentException("Can't populate data. Must be array or iterated object.");
        }

        foreach ($data as $key => $value) {
            $this->setWithoutValidation($key, $value);
        }

        return $this;
    }

    /**
     * Set property value
     *
     * @param string $name
     * @param mixed $value
     * @return AbstractModel
     * @throws \InvalidArgumentException
     */
    public function set($name, $value)
    {
        if ($value !== null) {
            if ($this->$name === $value) {
                return $this;
            }
            $value = static::getSchema()->getProperty($name)->filterAndValidate($value);
        }

        return parent::set($name, $value);
    }

    /**
     * Set property value
     *
     * @param string $name
     * @param mixed $value
     * @return AbstractModel|ModelInterface
     * @throws \InvalidArgumentException
     */
    public function setWithoutValidation($name, $value)
    {
        if ($value !== null) {
            $value = static::getSchema()->getProperty($name)->getFilterChain()->filter($value);
        }

        return parent::set($name, $value);
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