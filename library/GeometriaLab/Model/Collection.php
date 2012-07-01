<?php

namespace GeometriaLab\Model;

use GeometriaLab\Model\Schemaless\ModelInterface as SchemalessModelInterface;

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
     * @return CollectionInterface|Collection
     * @throws \InvalidArgumentException
     */
    public function push($data)
    {
        if ($data instanceof SchemalessModelInterface) {
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
     * @return SchemalessModelInterface|null
     */
    public function pop()
    {
        return array_pop($this->models);
    }

    /**
     * Add model or models at the beginning of a collection
     *
     * @param mixed $data
     * @return CollectionInterface|Collection
     * @throws \InvalidArgumentException
     */
    public function unshift($data)
    {
        if ($data instanceof SchemalessModelInterface) {
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
     * @return SchemalessModelInterface|null
     */
    public function shift()
    {
        return array_shift($this->models);
    }

    /**
     * Set model to collection by offset
     *
     * @param integer $offset
     * @param SchemalessModelInterface $model
     * @return CollectionInterface|Collection
     */
    public function set($offset, SchemalessModelInterface $model)
    {
        $this->models[$offset] = $model;

        return $this;
    }

    /**
     * Remove model from collection
     *
     * @param SchemalessModelInterface $model
     * @return CollectionInterface|Collection
     */
    public function remove(SchemalessModelInterface $model)
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
     * @return SchemalessModelInterface|null
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
     * @return SchemalessModelInterface|null
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
     * @return SchemalessModelInterface|null
     */
    public function getLast()
    {
        $model = end($this->models);

        return $model !== false ? $model : null;
    }

    /**
     * Shuffle collection
     *
     * @return CollectionInterface|Collection
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
     * @return CollectionInterface|Collection
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
     * @return CollectionInterface|Collection
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
     * @param integer $depth
     * @return array
     */
    public function toArray($depth = 0)
    {
        $array = array();
        foreach($this->models as $model) {
            if ($depth !== 0) {
                $array[] = $model->toArray($depth === -1 ? -1 : $depth - 1);
            } else {
                $array[] = $model;
            }
        }

        return $array;
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
                $callback = function(SchemalessModelInterface $model) use ($condition) {
                    /**
                     * @var SchemalessModelInterface $model
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
     * @return SchemalessModelInterface|null
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
     * @return SchemalessModelInterface|null
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