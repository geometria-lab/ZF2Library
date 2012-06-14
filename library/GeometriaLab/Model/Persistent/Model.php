<?php

namespace GeometriaLab\Model\Persistent;

use GeometriaLab\Model\Schema\Manager,
    GeometriaLab\Model\Persistent\Mapper;

class Model extends \GeometriaLab\Model\Model implements ModelInterface
{
    /**
     * Clean property values
     *
     * @var array
     */
    protected $cleanPropertyValues = array();

    /**
     * Save model to storage
     *
     * @return boolean
     */
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

    /**
     * Delete model from storage
     *
     * @return boolean
     */
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
     * @param string $name
     * @return boolean
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
     * @param string $name
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getChange($name)
    {
        if (!$this->getSchema()->hasProperty($name)) {
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
     * @param string $name
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function getClean($name)
    {
        if (!$this->getSchema()->hasProperty($name)) {
            throw new \InvalidArgumentException("Property '$name' does not exists");
        }

        if (isset($this->cleanPropertyValues[$name])) {
            return $this->cleanPropertyValues[$name];
        } else {
            return null;
        }
    }

    /**
     * Mark model as clean, or remove clean data
     *
     * @param boolean $flag
     * @return Model
     */
    public function markClean($flag = true)
    {
        $this->cleanPropertyValues = $flag ? $this->propertyValues : array();

        return $this;
    }

    /**
     * Get mapper
     *
     * @static
     * @return Mapper\MapperInterface
     */
    static public function getMapper()
    {
        $className = get_called_class();

        $mappers = Mapper\Manager::getInstance();

        if (!$mappers->has($className)) {
            $schemas = Schema\Manager::getInstance();
            if (!$schemas->has($className)) {
                $schema = $className::createSchema();
            } else {
                $schema = $schemas->get($className);
            }

            $mapperClassName = $schema->getMapperClass();
            /**
             * @var Mapper\MapperInterface
             */
            $mapper = new $mapperClassName($schema->getMapperOptions());
            $mapper->setModelClass($className);

            $mappers->add($className, $mapper);
        }

        return $mappers->get($className);
    }

    /**
     * Create persistent model schema
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
}