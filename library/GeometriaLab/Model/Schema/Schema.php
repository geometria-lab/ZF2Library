<?php

namespace GeometriaLab\Model\Schema;

use GeometriaLab\Model\Schema\Property\PropertyInterface;

class Schema implements SchemaInterface
{
    /**
     * Expected properties namespaces
     *
     * @var array
     */
    static protected $propertyNamespaces = array(
        'GeometriaLab\Model\Schema\Property',
    );

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
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function addProperty(PropertyInterface $property)
    {
        $this->validateProperty($property);
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
     * Remove property
     *
     * @param string $name
     * @return Schema
     * @throws \InvalidArgumentException
     */
    public function removeProperty($name)
    {
        if (!$this->hasProperty($name)) {
            throw new \InvalidArgumentException("Property '$name' not present in model '$this->className'");
        }

        unset($this->properties[$name]);

        return $this;
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

    /**
     * Validate property
     *
     * @param PropertyInterface $property
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    protected function validateProperty(PropertyInterface $property)
    {
        $name = $property->getName();

        if ($this->hasProperty($name)) {
            throw new \InvalidArgumentException("Property '$name' already exist in model '$this->className'");
        }

        $propertyReflection = new \ReflectionClass($property);
        $propertyNamespace = $propertyReflection->getNamespaceName();

        if (!in_array($propertyNamespace, static::$propertyNamespaces)) {
            throw new \RuntimeException("Property '$name' must be in '" . implode(', ', static::$propertyNamespaces) . "' namespaces, but $propertyNamespace is given");
        }
    }
}