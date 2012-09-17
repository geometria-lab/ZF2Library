<?php

namespace GeometriaLab\Model\Persistent;

use GeometriaLab\Model\Persistent\Mapper;

use GeometriaLab\Model\Persistent\Relation\BelongsTo,
    GeometriaLab\Model\Persistent\Relation\HasOne,
    GeometriaLab\Model\Persistent\Relation\HasMany;

use GeometriaLab\Model\Persistent\Schema\Property\Relation\AbstractRelation as AbstractRelationProperty;

abstract class AbstractModel extends \GeometriaLab\Model\AbstractModel implements ModelInterface
{
    /**
     * Clean property values
     *
     * @var array
     */
    protected $cleanPropertyValues = array();

    /**
     * Relations
     *
     * @var array
     */
    protected $relations = array();

    /**
     * @var string
     */
    static protected $parserClassName = 'GeometriaLab\Model\Persistent\Schema\DocBlockParser';

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
     * Set property value
     *
     * @param string $name
     * @param mixed $value
     * @return AbstractModel|ModelInterface
     * @throws \InvalidArgumentException
     */
    public function set($name, $value)
    {
        if ($this->hasRelation($name)) {
            $relation = $this->getRelation($name);

            if ($relation instanceof BelongsTo || $relation instanceof HasOne) {
                $relation->setTargetModel($value);
            } else if ($relation instanceof HasMany) {
                $relation->setTargetModels($value);
            }
        } else {
            parent::set($name, $value);

            foreach ($this->getRelations() as $relation) {
                // @todo If changed referenced key?
                /**
                 * @var BelongsTo $relation
                 */
                if ($relation instanceof BelongsTo && $relation->getProperty()->getOriginProperty() === $name) {
                    $relation->resetTargetModel();
                }
            }
        }

        return $this;
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

        $property = $schema->getProperty($name);

        if (!$property instanceof AbstractRelationProperty) {
            throw new \InvalidArgumentException("'$name' is not relation");
        }

        return $this->relations[$name];
    }

    /**
     * Get relations
     *
     * @return array
     */
    public function getRelations()
    {
        return $this->relations;
    }

    /**
     * Has relation
     *
     * @param string $name
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function hasRelation($name)
    {
        $schema = static::getSchema();

        if (!$schema->hasProperty($name)) {
            return false;
        }

        $property = $schema->getProperty($name);

        return ($property instanceof AbstractRelationProperty);
    }

    /**
     * Save model to storage
     *
     * @param bool $validate
     * @return bool
     * @throws \RuntimeException
     */
    public function save($validate = true)
    {
        if ($validate && !$this->isValid()) {
            $errorString = '';
            foreach ($this->getErrorMessages() as $fieldName => $errors) {
                $errorString .= "Field $fieldName:\r\n" . implode("\r\n", $errors) . "\r\n";
            }
            throw new \RuntimeException("Model is invalid: $errorString");
        }

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
                $this->relations[$name] = new $relationClassName($this, $property);
            } else if ($property->getDefaultValue() !== null) {
                $this->set($name, $property->getDefaultValue());
            }
        }
    }
}