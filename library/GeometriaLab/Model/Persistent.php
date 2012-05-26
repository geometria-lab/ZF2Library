<?php

namespace GeometriaLab\Model;

use GeometriaLab\Model\Persistent\Mapper;

abstract class Persistent extends Model
{
    /**
     * Clean property values
     *
     * @var array
     */
    protected $cleanPropertyValues = array();


    public function save()
    {

    }

    public function delete()
    {
        return static::getMapper()->delete($this);
    }

    /**
     * Is not saved model
     *
     * @return bool
     */
    public function isNew()
    {
        return empty($this->cleanPropertyValues);
    }

    public function isChanged()
    {
        foreach($this->getProperties() as $property) {
            if ($this->isPropertyChanged($property->getName())) {
                return true;
            }
        }

        return false;
    }

    public function isPropertyChanged($name)
    {
        return $this->getClean($name) !== $this->get($name);
    }

    public function getChangedProperties()
    {
        $changedProperties = array();
        foreach($this->getProperties() as $property) {
            if ($this->isPropertyChanged($property->getName())) {
                $changedProperties[] = $property->getName();
            }
        }

        return $changedProperties;
    }

    public function getChange($name)
    {
        $property = $this->getPropertyDefinition($name);
        if ($property === null) {
            throw new \InvalidArgumentException("Property '$name' does not exists");
        }

        $change = array(null, $this->get($name));

        if (isset($this->cleanPropertyValues[$name])) {
            return $change[0] = $this->cleanPropertyValues[$name];
        }

        return $change;
    }

    public function getChanges()
    {
        $changes = array();
        foreach($this->getChangedProperties() as $name) {
            $changes[$name] = $this->getChange($name);
        }
        return $changes;
    }

    public function getClean($name)
    {
        $property = $this->getPropertyDefinition($name);
        if ($property === null) {
            throw new \InvalidArgumentException("Property '$name' does not exists");
        }

        if (isset($this->cleanPropertyValues[$name])) {
            return $this->cleanPropertyValues[$name];
        } else {
            return null;
        }
    }

    public function markClean()
    {
        $this->cleanPropertyValues = $this->propertyValues;

        return $this;
    }

    /**
     * Get mapper
     *
     * @static
     * @return Persistent\Mapper\MapperInterface
     */
    public static function getMapper()
    {
        /**
         * @var Persistent $className
         */
        $className = get_called_class();

        $mappers = Mapper\Manager::getInstance();

        if (!$mappers->has($className)) {
            $definitions = Definition\Manager::getInstance();
            if (!$definitions->has($className)) {
                $className::createDefinition();
            }

            /**
             * @var Persistent\Definition $definition
             */
            $definition = $definitions->get($className);

            $mappers->add($className, $definition->createMapper());
        }

        return $mappers->get($className);
    }

    /**
     * Create persistent model definition
     *
     * @return Persistent\Definition
     */
    public static function createDefinition()
    {
        $definitions = Definition\Manager::getInstance();

        $className = get_called_class();

        if (!$definitions->has($className)) {
            $definitions->add(new Persistent\Definition($className));
        }

        return $definitions->get($className);
    }
}