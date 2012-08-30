<?php

namespace GeometriaLab\Model\Persistent;

use GeometriaLab\Model\Persistent\Mapper;

use GeometriaLab\Model\Persistent\Relation\BelongsTo,
    GeometriaLab\Model\Persistent\Relation\HasOne,
    GeometriaLab\Model\Persistent\Relation\HasMany;

use GeometriaLab\Model\Persistent\Schema\Property\Relation\AbstractRelation as AbstractRelationProperty,
    GeometriaLab\Model\Persistent\Schema\Property\Relation\BelongsTo        as BelongsToProperty,
    GeometriaLab\Model\Persistent\Schema\Property\Relation\HasOne           as HasOneProperty,
    GeometriaLab\Model\Persistent\Schema\Property\Relation\HasMany          as HasManyProperty;

abstract class AbstractModel extends \GeometriaLab\Model\AbstractModel implements ModelInterface
{
    static protected $schemaClassName = 'GeometriaLab\Model\Persistent\Schema\Schema';

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
            return $value->getTargetModel();
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
     * Get relation
     *
     * @param string $name
     * @return BelongsTo|HasMany|HasOne
     * @throws \InvalidArgumentException
     */
    public function getRelation($name)
    {
        $schema = static::getSchema();

        if (!$schema->hasProperty($name)) {
            throw new \InvalidArgumentException("Relation '$name' does not exists");
        }

        $property = $schema->getProperty($name);

        if (!$property instanceof AbstractRelationProperty) {
            throw new \InvalidArgumentException("'$name' is not relation");
        }

        return $this->propertyValues[$name];
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
            throw new \InvalidArgumentException("Property '$name' does not exists");
        }

        $property = $schema->getProperty($name);

        if ($value !== null) {
            try {
                $value = $property->prepare($value);
            } catch (\InvalidArgumentException $e) {
                throw new \InvalidArgumentException("Invalid value for property '$name': " . $e->getMessage());
            }
        }

        $method = "set{$name}";
        if (method_exists($this, $method)) {
            call_user_func(array($this, $method), $value);
        } else if ($property instanceof BelongsToProperty) {
            $this->propertyValues[$name]->setTargetModel($value);
        } else if ($property instanceof HasOneProperty) {
            $this->propertyValues[$name]->setTargetModel($value);
        } else if ($property instanceof HasManyProperty) {
            $this->propertyValues[$name]->setTargetModels($value);
        } else {
            $this->propertyValues[$name] = $value;

            foreach($schema->getProperties() as $property) {
                // @todo If changed referenced key?
                /**
                 * @var BelongsToProperty $property
                 */
                if ($property instanceof BelongsTo && $property->getOriginProperty() === $name) {
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
        $schema = static::getSchema();

        if ($schema->hasProperty($name)) {
            $property = $schema->getProperty($name);
            /**
             * @var \GeometriaLab\Model\Persistent\Schema\Property\PropertyInterface $property
             */
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
        $schema = static::getSchema();

        if (!$schema->hasProperty($name)) {
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
     * @return AbstractModel
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
            /**
             * @var Schema\Schema $schema
             */
            $schema = call_user_func(array($className, 'getSchema'));

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
     * Setup model
     */
    protected function setup()
    {
        // Create relations and fill default values

        /**
         * @var \GeometriaLab\Model\Persistent\Schema\Property\PropertyInterface $property
         */
        foreach($this->getProperties() as $name => $property) {
            if ($property instanceof AbstractRelationProperty) {
                /**
                 * @var AbstractRelationProperty $property
                 */
                $relationClassName = $property->getRelationClass();
                $this->propertyValues[$name] = new $relationClassName($this, $property);
            } else if ($property->getDefaultValue() !== null) {
                $this->set($name, $property->getDefaultValue());
            }
        }
    }
}