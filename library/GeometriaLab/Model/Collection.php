<?php

class GeometriaLab_Model_Collection implements Iterator, Countable, ArrayAccess
{
    /**
     * Models
     *
     * @var array
     */
    protected $_models = array();

    /**
     * Iterator position
     *
     * @var integer
     */
    protected $_iteratorPosition = 0;

    /**
     * Constructor
     *
     * @param null|array|Traversable $data
     */
    public function __construct($data = null)
    {
        $this->populate($data);
    }

    /**
     * Populate collection from array or iterable object
     *
     * @param mixed $data Data must be array or iterable object
     *
     * @return GeometriaLab_Model_Collection
     * @throws GeometriaLab_Model_Exception
     */
    public function populate($data)
    {
        $this->clear();

        try {
            $this->append($data);
        } catch (GeometriaLab_Model_Collection_Exception $e) {
            throw new GeometriaLab_Model_Collection_Exception('Populated data must be array or iterated object.');
        }

        return $this;
    }

    /**
     * Append to collection from model or other collection
     *
     * @param mixed $data
     * @return GeometriaLab_Model_Collection
     */
    public function append($data)
    {
        if ($data instanceof GeometriaLab_Model) {
            $this->_models[] = $data;
        } else if (self::_isIterable($data)) {
            foreach ($data as $model) {
                $this->_models[] = $model;
            }
        } else {
            throw new GeometriaLab_Model_Collection_Exception('Append data must be model, array or iterated object.');
        }

        return $this;
    }

    /**
     * Prepend to collection from model or other collection
     *
     * @param GeometriaLab_Model|GeometriaLab_Model_Collection $modelOrCollection
     * @return GeometriaLab_Model_Collection
     */
    public function prepend($modelOrCollection)
    {
        $this->reverse();
        $this->append($modelOrCollection);
        $this->reverse();

        return $this;
    }

    /**
     * Set to collection
     *
     * @param $offset
     * @param GeometriaLab_Model $model
     * @return GeometriaLab_Model_Collection
     */
    public function set($offset, GeometriaLab_Model $model)
    {
        $this->_models[$offset] = $model;

        return $this;
    }

    /**
     * Get from collection
     *
     * @param $offset
     * @return GeometriaLab_Model|null
     */
    public function get($offset)
    {
        if (isset($this->_models[$offset])) {
            return $this->_models[$offset];
        } else {
            return null;
        }
    }

    /**
     * Get first model
     *
     * @return GeometriaLab_Model|null
     */
    public function getFirst()
    {
        if (isset($this->_models[0])) {
            return $this->_models[0];
        } else {
            return null;
        }
    }

    /**
     * Get last model
     *
     * @return GeometriaLab_Model|null
     */
    public function getLast()
    {
        return end($this->_models);
    }

    /**
     * Shuffle collection
     *
     * @return GeometriaLab_Model_Collection
     */
    public function shuffle()
    {
        shuffle($this->_models);

        $this->rewind();

        return $this;
    }

    /**
     * Reverse collection
     *
     * @return GeometriaLab_Model_Collection
     */
    public function reverse()
    {
        $this->_models = array_reverse($this->_models);

        $this->rewind();

        return $this;
    }

    /**
     * Clear collection
     *
     * @return GeometriaLab_Model_Collection
     */
    public function clear()
    {
        $this->_models = array();

        $this->rewind();

        return $this;
    }

    /**
     * Is empty?
     *
     * @return bool
     */
    public function isEmpty()
    {
        return 0 == $this->count();
    }

    /**
     * To array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->_models;
    }

    /**
     * Is iterable
     *
     * @static
     * @param $data
     * @return bool
     */
    static protected function _isIterable($data)
    {
        if (self::$_isIterableValidator === null) {
            self::$_isIterableValidator = new GeometriaLab_Validate_IsIterable();
        }

        return self::$_isIterableValidator->isValid($data);
    }

    /*
    * Methods implements Iterator
    */

    /**
     * @return GeometriaLab_Model|null
     */
    public function current()
    {
        if (isset($this->_models[$this->_iteratorPosition])) {
            return $this->_models[$this->_iteratorPosition];
        } else {
            return null;
        }
    }

    public function next()
    {
        $this->_iteratorPosition++;
    }

    public function key()
    {
        return $this->_iteratorPosition;
    }

    public function valid()
    {
        return $this->_iteratorPosition < $this->count();
    }

    public function rewind()
    {
        $this->_iteratorPosition = 0;
    }

    /**
     * Methods implements Countable
     */

    public function count()
    {
        return count($this->_models);
    }

    /**
     * Methods implements ArrayAccess
     */

    public function offsetExists($offset)
    {
        return isset($this->_models[$offset]);
    }

    /**
     * @return GeometriaLab_Model|null
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        if ($offset === null) {
            $this->append($value);
        } else {
            $this->set($offset, $value);
        }

        return $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->_models[$offset]);
    }
}