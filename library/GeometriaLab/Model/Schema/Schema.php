<?php

namespace GeometriaLab\Model\Schema;

use GeometriaLab\Model\Schema\Property\PropertyInterface;

class Schema implements SchemaInterface
{
    /**
     * Class name
     *
     * @var string
     */
    protected $className;

    /**
     * Properties
     *
     * @var PropertyInterface[]
     */
    protected $properties = array();

    /**
     * Set class name
     *
     * @param $className
     * @return Schema
     */
    public function setClassName($className)
    {
        $this->className = $className;

        return $this;
    }

    /**
     * Get class name
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Get property
     *
     * @param string $name
     * @return PropertyInterface
     * @throws \InvalidArgumentException
     */
    public function getProperty($name)
    {
        if (!$this->hasProperty($name)) {
            throw new \InvalidArgumentException("Property '$name' not present in model '$this->className'");
        }

        return $this->properties[$name];
    }

    /**
     * Set property
     *
     * @param PropertyInterface $property
     * @return Schema
     */
    public function setProperty(PropertyInterface $property)
    {
        $this->properties[$property->getName()] = $property;

        return $this;
    }

    /**
     * Has property?
     *
     * @param string $name
     * @return bool
     */
    public function hasProperty($name)
    {
        return isset($this->properties[$name]);
    }

    /**
     * Get all properties
     *
     * @return PropertyInterface[]
     */
    public function getProperties()
    {
        return $this->properties;
    }
}