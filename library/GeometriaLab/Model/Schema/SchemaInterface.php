<?php

namespace GeometriaLab\Model\Schema;

use GeometriaLab\Model\Schema\Property\PropertyInterface;

interface SchemaInterface
{
    /**
     * Set class name
     *
     * @param $className
     * @return SchemaInterface
     */
    public function setClassName($className);

    /**
     * Get class name
     *
     * @return string
     */
    public function getClassName();

    /**
     * Get property
     *
     * @param string $name
     * @return PropertyInterface
     * @throws \Exception
     */
    public function getProperty($name);
    /**
     * Set property
     *
     * @param PropertyInterface $property
     * @return SchemaInterface
     */
    public function setProperty(PropertyInterface $property);

    /**
     * Has property?
     *
     * @param string $name
     * @return bool
     */
    public function hasProperty($name);
    /**
     * Get all properties
     *
     * @return PropertyInterface[]
     */
    public function getProperties();
}