<?php

namespace GeometriaLab\Model;

interface CollectionInterface extends \Iterator, \Countable, \ArrayAccess
{
    /**
     * Constructor
     *
     * @param mixed $data
     */
    public function __construct($data = null);

    /**
     * Add model or models to the end of a collection
     *
     * @param mixed $data
     * @return CollectionInterface
     * @throws \InvalidArgumentException
     */
    public function push($data);

    /**
     * Remove and return last model
     *
     * @return ModelInterface|null
     */
    public function pop();

    /**
     * Add model or models at the beginning of a collection
     *
     * @param mixed $data
     * @return CollectionInterface
     * @throws \InvalidArgumentException
     */
    public function unshift($data);

    /**
     * Remove and return first model
     *
     * @return ModelInterface|null
     */
    public function shift();

    /**
     * Set model to collection by offset
     *
     * @param integer $offset
     * @param ModelInterface $model
     * @return CollectionInterface
     */
    public function set($offset, ModelInterface $model);

    /**
     * Remove model from collection
     *
     * @param ModelInterface $model
     * @return CollectionInterface
     */
    public function remove(ModelInterface $model);

    /**
     * Get model from collection by offset
     *
     * @param integer $offset
     * @return ModelInterface|null
     */
    public function get($offset);

    /**
     * Get first model
     *
     * @return ModelInterface|null
     */
    public function getFirst();

    /**
     * Get last model
     *
     * @return ModelInterface|null
     */
    public function getLast();

    /**
     * Shuffle collection
     *
     * @return CollectionInterface
     */
    public function shuffle();

    /**
     * Reverse collection
     *
     * @return CollectionInterface
     */
    public function reverse();

    /**
     * Clear collection
     *
     * @return CollectionInterface
     */
    public function clear();

    /**
     * Is empty?
     *
     * @return boolean
     */
    public function isEmpty();

    /**
     * To array
     *
     * @return ModelInterface[]
     */
    public function toArray();

    /**
     * Get models by condition or callback
     *
     * @param mixed $condition
     * @return CollectionInterface
     */
    public function getByCondition($condition);

    /**
     * Get slice of collection
     *
     * @param integer      $offset
     * @param integer|null $length
     * @return CollectionInterface
     */
    public function getSlice($offset, $length = null);

    /**
     * Sort collection by callback
     *
     * @param mixed $callback
     * @return CollectionInterface
     */
    public function sort($callback);
}