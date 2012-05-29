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
        if ($this->isNew()) {
            return static::getMapper()->create($this);
        } else if ($this->isChanged()) {
            return static::getMapper()->update($this);
        } else {
            return false;
        }
    }

    public function delete()
    {
        return static::getMapper()->delete($this);
    }

    /**
     * Is not saved model
     *
     * @return boolean
     */
    public function isNew()
    {
        return empty($this->cleanPropertyValues);
    }

    /**
     * Is model changed
     *
     * @return boolean
     */
    public function isChanged()
    {
        foreach($this->getProperties() as $property) {
            if ($this->isPropertyChanged($property->getName())) {
                return true;
            }
        }

        return false;
    }

    /**
     * Is property changed
     *
     * @param $name
     * @return bool
     */
    public function isPropertyChanged($name)
    {
        return $this->getClean($name) !== $this->get($name);
    }

    /**
     * Get changed property
     *
     * @return array
     */
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

    /**
     * Get property change
     *
     * @param $name
     * @return array
     * @throws \InvalidArgumentException
     */
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

    /**
     * Get model changes
     *
     * @return array
     */
    public function getChanges()
    {
        $changes = array();
        foreach($this->getChangedProperties() as $name) {
            $changes[$name] = $this->getChange($name);
        }
        return $changes;
    }

    /**
     * Get clean property value
     *
     * @param $name
     * @return mixed
     * @throws \InvalidArgumentException
     */
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

    /**
     * Mark model as clean
     *
     * @return Persistent
     */
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