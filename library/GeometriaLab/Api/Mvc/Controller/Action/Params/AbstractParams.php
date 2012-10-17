<?php

namespace GeometriaLab\Api\Mvc\Controller\Action\Params;

use GeometriaLab\Model\AbstractModel,
    GeometriaLab\Model\Persistent\Relation\BelongsTo,
    GeometriaLab\Model\Schema\Property\Validator\Exception\InvalidValueException,
    GeometriaLab\Api\Mvc\Controller\Action\Params\Schema\Property\Relation\BelongsTo as BelongsToProperty;

/**
 *
 */
abstract class AbstractParams extends AbstractModel
{
    /**
     * Parser class name
     *
     * @var string
     */
    static protected $parserClassName = 'GeometriaLab\Api\Mvc\Controller\Action\Params\Schema\DocBlockParser';

    /**
     * Relations
     *
     * @var array
     */
    protected $relations = array();

    /**
     * Non-existent properties, which was set
     *
     * @var array
     */
    protected $notPresentProperties = array();

    /**
     * Populate model from array or iterable object
     *
     * @param array|\Traversable|\stdClass $data  Model data (must be array or iterable object)
     * @param bool $notValidate
     * @return AbstractParams
     * @throws \InvalidArgumentException
     */
    public function populate($data, $notValidate = false)
    {
        if (!is_array($data) && !$data instanceof \Traversable && !$data instanceof \stdClass) {
            throw new \InvalidArgumentException("Can't populate data. Must be array or iterated object.");
        }

        foreach ($data as $key => $value) {
            if (!$this->has($key)) {
                $this->notPresentProperties[$key] = $value;
                continue;
            }
            try {
                // We always need validation hear!
                $this->set($key, $value);
            } catch (InvalidValueException $e) {
                $this->errorMessages[$key] = $e->getValidationErrorMessages();
            } catch (\InvalidArgumentException $e) {
                // Do nothing, keep silent...
            }
        }

        return $this;
    }

    /**
     * Get object property
     *
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        if ($this->hasRelation($name)) {
            $value = $this->getRelation($name)->getTargetModel();
        } else {
            $value = parent::get($name);
        }

        return $value;
    }

    /**
     * Set property value
     *
     * @param string $name
     * @param mixed $value
     * @param bool $notValidate
     * @return AbstractParams
     */
    public function set($name, $value, $notValidate = false)
    {
        if ($this->hasRelation($name)) {
            $this->getRelation($name)->setTargetModel($value);
        } else {
            // We always need validation hear!
            parent::set($name, $value);

            foreach ($this->getRelations() as $relation) {
                /* @var BelongsTo $relation */
                if ($relation->getProperty()->getOriginProperty() === $name) {
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
     * @return BelongsTo
     * @throws \InvalidArgumentException
     */
    public function getRelation($name)
    {
        $property = static::getSchema()->getProperty($name);

        if (!$property instanceof BelongsToProperty) {
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

        return ($property instanceof BelongsToProperty);
    }

    /**
     * Is Valid model data
     *
     * @return bool
     */
    public function isValid()
    {
        $result = parent::isValid();

        foreach ($this->notPresentProperties as $name => $value) {
            $this->errorMessages[$name] = array('notPresent' => "Property does not exists");
            $result = false;
        }

        return $result;
    }

    /**
     * Create relations and fill default values
     */
    protected function setup()
    {
        /* @var \GeometriaLab\Model\Persistent\Schema\Property\PropertyInterface $property */
        foreach($this->getProperties() as $name => $property) {
            if ($property instanceof BelongsToProperty) {
                /* @var BelongsToProperty $property */
                $relationClassName = $property->getRelationClass();
                $this->relations[$name] = new $relationClassName($this, $property);
            } elseif ($property->getDefaultValue() !== null) {
                $this->set($name, $property->getDefaultValue());
            }
        }
    }
}