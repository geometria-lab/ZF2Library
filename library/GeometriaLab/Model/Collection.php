<?php

namespace GeometriaLab\Model;

class Collection implements CollectionInterface
{
    /**
     * Models
     *
     * @var array
     */
    protected $models = array();

    /**
     * Iterator position
     *
     * @var integer
     */
    protected $iteratorPosition = 0;

    /**
     * Constructor
     *
     * @param mixed $data
     */
    public function __construct($data = null)
    {
        if ($data !== null) {
            $this->push($data);
        }
    }

    /**
     * Add model or models to the end of a collection
     *
     * @param mixed $data
     * @return Collection
     * @throws \InvalidArgumentException
     */
    public function push($data)
    {
        if ($data instanceof ModelInterface) {
            array_push($this->models, $data);
        } else if (is_array($data) || $data instanceof \Traversable) {
            // TODO: Create models?

            foreach ($data as $model) {
                $this->push($model);
            }
        } else {
            throw new \InvalidArgumentException('Data must be model, array or iterated object.');
        }

        return $this;
    }

    /**
     * Remove and return last model
     *
     * @return ModelInterface|null
     */
    public function pop()
    {
        return array_pop($this->models);
    }

    /**
     * Add model or models at the beginning of a collection
     *
     * @param mixed $data
     * @return Collection
     * @throws \InvalidArgumentException
     */
    public function unshift($data)
    {
        if ($data instanceof ModelInterface) {
            array_unshift($this->models, $data);
        } else if (is_array($data) || $data instanceof \Traversable) {
            foreach ($data as $model) {
                $this->unshift($model);
            }
        } else {
            throw new \InvalidArgumentException('Data must be model, array or iterated object.');
        }

        return $this;
    }

    /**
     * Remove and return first model
     *
     * @return ModelInterface|null
     */
    public function shift()
    {
        return array_shift($this->models);
    }

    /**
     * Set model to collection by offset
     *
     * @param integer $offset
     * @param ModelInterface $model
     * @return Collection
     */
    public function set($offset, ModelInterface $model)
    {
        $this->models[$offset] = $model;

        return $this;
    }

    /**
     * Remove model from collection
     *
     * @param ModelInterface $model
     * @return Collection
     */
    public function remove(ModelInterface $model)
    {
        $offset = array_search($model, $this->models, true);

        if ($offset) {
            unset($this->models[$offset]);
            $this->models = array_values($this->models);
            // TODO: rewind?
        }

        return $this;
    }

    /**
     * Get model from collection by offset
     *
     * @param integer $offset
     * @return ModelInterface|null
     */
    public function get($offset)
    {
        if (isset($this->models[$offset])) {
            return $this->models[$offset];
        } else {
            return null;
        }
    }

    /**
     * Get first model
     *
     * @return ModelInterface|null
     */
    public function getFirst()
    {
        if (isset($this->models[0])) {
            return $this->models[0];
        } else {
            return null;
        }
    }

    /**
     * Get last model
     *
     * @return ModelInterface|null
     */
    public function getLast()
    {
        return end($this->models);
    }

    /**
     * Shuffle collection
     *
     * @return Collection
     */
    public function shuffle()
    {
        shuffle($this->models);

        $this->rewind();

        return $this;
    }

    /**
     * Reverse collection
     *
     * @return Collection
     */
    public function reverse()
    {
        $this->models = array_reverse($this->models);

        $this->rewind();

        return $this;
    }

    /**
     * Clear collection
     *
     * @return Collection
     */
    public function clear()
    {
        $this->models = array();

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
        return $this->models;
    }

    /**
     * Get models by condition or callback
     *
     * @param mixed $condition
     * @return Collection
     */
    public function getByCondition($condition)
    {
        /**
         * @var Collection $collection
         */
        $collection = new $this;

        if (count($this)) {
            if (!is_callable($condition)) {
                $callback = function(ModelInterface $model) use ($condition) {
                    /**
                     * @var ModelInterface $model
                     */
                    foreach ($condition as $name => $value) {
                        if ($model->get($name) != $value) {
                            return false;
                        }
                    }
                    return true;
                };
            } else {
                $callback = $condition;
            }

            $data = array_filter($this->models, $callback);
            $collection->push($data);
        }

        return $collection;
    }

    /**
     * Get slice of collection
     *
     * @param integer      $offset
     * @param integer|null $length
     * @return Collection
     */
    public function getSlice($offset, $length = null)
    {
        /**
         * @var Collection $collection
         */
        $collection = new $this;

        if (count($this)) {
            $models = array_slice($this->models, $offset, $length);
            $collection->push($models);
        }

        return $collection;
    }

    /**
     * Sort collection by callback
     *
     * @param mixed $callback
     * @return Collection
     */
    public function sort($callback)
    {
        usort($this->models, $callback);
        $this->rewind();

        return $this;
    }

    /*
    * Methods implements Iterator
    */

    /**
     * @return ModelInterface|null
     */
    public function current()
    {
        if (isset($this->models[$this->iteratorPosition])) {
            return $this->models[$this->iteratorPosition];
        } else {
            return null;
        }
    }

    public function next()
    {
        $this->iteratorPosition++;
    }

    public function key()
    {
        return $this->iteratorPosition;
    }

    public function valid()
    {
        return $this->iteratorPosition < $this->count();
    }

    public function rewind()
    {
        $this->iteratorPosition = 0;
    }

    /**
     * Methods implements Countable
     */

    /**
     * Models count
     *
     * @return integer
     */
    public function count()
    {
        return count($this->models);
    }

    /**
     * Methods implements ArrayAccess
     */

    /**
     * @param integer $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->models[$offset]);
    }

    /**
     * @param integer $offset
     * @return ModelInterface|null
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        if ($offset === null) {
            $this->push($value);
        } else {
            $this->set($offset, $value);
        }

        return $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->models[$offset]);
        $this->models = array_values($this->models);
        // TODO: rewind?
    }
}