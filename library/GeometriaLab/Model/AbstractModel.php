<?php

namespace GeometriaLab\Model;

use GeometriaLab\Model\Schema\SchemaInterface,
    GeometriaLab\Model\Schema\Property\PropertyInterface,
    GeometriaLab\Model\Schema\Manager as SchemaManager;

abstract class AbstractModel extends Schemaless\Model implements ModelInterface
{
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
     * Populate model from array or iterable object
     *
     * @param array|\Traversable|\stdClass $data  Model data (must be array or iterable object)
     * @return AbstractModel
     * @throws \InvalidArgumentException
     */
    public function populateSilent($data)
    {
        if (!is_array($data) && !$data instanceof \Traversable && !$data instanceof \stdClass) {
            throw new \InvalidArgumentException("Can't populate data. Must be array or iterated object.");
        }

        foreach ($data as $key => $value) {
            $this->setSilent($key, $value);
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

        return parent::get($name);
    }

    /**
     * Set property value
     *
     * @param string $name
     * @param mixed $value
     * @return AbstractModel|ModelInterface
     * @throws \InvalidArgumentException
     */
    public function set($name, $value)
    {
        $schema = static::getSchema();

        if (!$schema->hasProperty($name)) {
            throw new \InvalidArgumentException("Property '$name' does not exists", 1);
        }

        if ($value !== null) {
            $property = $schema->getProperty($name);

            $value = $property->getFilterChain()->filter($value);

            if (!$property->getValidatorChain()->isValid($value)) {
                throw new \InvalidArgumentException("Invalid property '$name' value: " . implode("\r\n", $property->getValidatorChain()->getMessages()), 2);
            }
        }

        return parent::set($name, $value);
    }

    /**
     * Set property value without throwing exception on validation
     *
     * @param string $name
     * @param mixed $value
     * @return AbstractModel|ModelInterface
     * @throws \InvalidArgumentException
     */
    public function setSilent($name, $value)
    {
        try {
            $this->set($name, $value);
        } catch (\InvalidArgumentException $e) {
            if ($e->getCode() === 1) {
                throw $e;
            }
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
     * Is Valid model data
     *
     * @return bool
     */
    public function isValid()
    {
        $this->errorMessages = array();
        $result = true;

        foreach ($this->getSchema()->getProperties() as $property) {
            $name = $property->getName();
            $value = $this->get($name);
            $messages = $property->getValidatorChain()->getMessages();

            if (!empty($messages)) {
                $this->errorMessages[$name] = $messages;
                $result = false;
            }

            if ($property instanceof \GeometriaLab\Model\Schema\Property\ModelProperty) {
                /** @var ModelInterface $value */
                $subModelMessages = $value->getErrorMessages();
                if (!empty($subModelMessages)) {
                    if (!isset($this->errorMessages[$name])) {
                        $this->errorMessages[$name] = array();
                    }
                    $this->errorMessages[$name]['InvalidModel'] = $subModelMessages;
                    $result = false;
                }
            }

            if ($property->isRequired() && $value === null) {
                $this->errorMessages[$name]['Required'] = "Is required";
                $result = false;
            }
        }

        return $result;
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