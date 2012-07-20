<?php

namespace GeometriaLab\Model\Persistent;

use GeometriaLab\Model\Persistent\Schema\Schema,
    GeometriaLab\Model\Schema\Manager as SchemaManager,
    GeometriaLab\Model\Persistent\Mapper;

use GeometriaLab\Model\Persistent\Relation\BelongsTo,
    GeometriaLab\Model\Persistent\Relation\HasOne,
    GeometriaLab\Model\Persistent\Relation\HasMany;

use GeometriaLab\Model\Persistent\Schema\Property\Relation\AbstractRelation as AbstractRelationProperty,
    GeometriaLab\Model\Persistent\Schema\Property\Relation\BelongsTo        as BelongsToProperty,
    GeometriaLab\Model\Persistent\Schema\Property\Relation\HasOne           as HasOneProperty,
    GeometriaLab\Model\Persistent\Schema\Property\Relation\HasMany          as HasManyProperty;

/**
 * @todo Abstract?
 */
class Model extends \GeometriaLab\Model\Model implements ModelInterface
{
    /**
     * Clean property values
     *
     * @var array
     */
    protected $cleanPropertyValues = array();

    /**
     * Get property value
     *
     * @param $name
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function get($name)
    {
        $value = parent::get($name);

        if ($value instanceof BelongsTo) {
            /**
             * @var BelongsTo $value
             */
            return $value->getReferencedModel();
        } else if ($value instanceof HasOne) {
            /**
             * @var HasOne $value
             */
            return $value->getTargetModel();
        } else if ($value instanceof HasMany) {
            /**
             * @var HasMany $value
             */
            return $value->getTargetModels();
        }

        return $value;
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
        parent::set($name, $value);

        $property = $this->getSchema()->getProperty($name);

        if ($property instanceof BelongsToProperty) {
            $this->propertyValues[$name]->setReferencedModel($value);
        } else if ($property instanceof HasOneProperty) {
            $this->propertyValues[$name]->setForeignModel($value);
        } else if ($property instanceof HasManyProperty) {
            $this->propertyValues[$name]->setForeignModels($value);
        } else {
            foreach(static::getSchema()->getProperties() as $property) {
                // @todo If changed referenced key?
                if ($property instanceof BelongsTo && $property->getForeignProperty() === $name) {
                    $this->propertyValues[$property->getName()] = null;
                }
            }
        }

        return $this;
    }

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
        if (!$this->isNew()) {
            return static::getMapper()->delete($this);
        } else {
            return false;
        }
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
        if ($this->getSchema()->hasProperty($name)) {
            $property = $this->getSchema()->getProperty($name);
            if (!$property->isPersistent()) {
                return false;
            }
        }
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
            $schemas = SchemaManager::getInstance();

            /**
             * @var Schema $schema
             */
            if (!$schemas->has($className)) {
                /**
                 * @var ModelInterface $className
                 */
                $schema = $className::createSchema();
            } else {
                $schema = $schemas->get($className);
            }

            $mapperClassName = $schema->getMapperClass();
            /**
             * @var Mapper\MapperInterface $mapper
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
        $schemas = SchemaManager::getInstance();

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

        // Create relations and fill default values

        /**
         * @var \GeometriaLab\Model\Persistent\Schema\Property\PropertyInterface $property
         */
        foreach($this->getProperties() as $name => $property) {
            if ($property instanceof AbstractRelationProperty) {
                $relationClassName = $property->getRelationClass();
                $this->propertyValues[$name] = new $relationClassName($this, $property);
            } else if ($property->getDefaultValue() !== null) {
                $this->set($name, $property->getDefaultValue());
            }
        }
    }
}