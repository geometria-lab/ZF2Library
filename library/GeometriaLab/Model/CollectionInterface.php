<?php

namespace GeometriaLab\Model;

use GeometriaLab\Model\Schemaless\ModelInterface as SchemalessModelInterface;

interface CollectionInterface extends \Iterator, \Countable, \ArrayAccess
{
    /**
     * Add model or models to the end of a collection
     *
     * @param SchemalessModelInterface|\Traversable|array $data
     * @return CollectionInterface|Collection
     * @throws \InvalidArgumentException
     */
    public function push($data);

    /**
     * Remove and return last model
     *
     * @return SchemalessModelInterface|null
     */
    public function pop();

    /**
     * Add model or models at the beginning of a collection
     *
     * @param SchemalessModelInterface|\Traversable|array $data
     * @return CollectionInterface|Collection
     * @throws \InvalidArgumentException
     */
    public function unshift($data);

    /**
     * Remove and return first model
     *
     * @return SchemalessModelInterface|null
     */
    public function shift();

    /**
     * Set model to collection by offset
     *
     * @param integer $offset
     * @param SchemalessModelInterface $model
     * @return CollectionInterface|Collection
     */
    public function set($offset, SchemalessModelInterface $model);

    /**
     * Get model from collection by offset
     *
     * @param integer $offset
     * @return SchemalessModelInterface|null
     */
    public function get($offset);

    /**
     * Get first model
     *
     * @return SchemalessModelInterface|null
     */
    public function getFirst();

    /**
     * Get last model
     *
     * @return SchemalessModelInterface|null
     */
    public function getLast();

    /**
     * Get models by callback
     *
     * @param callable $callback
     * @return Collection
     */
    public function getByCallback($callback);

    /**
     * Get models by condition
     *
     * @param mixed $condition
     * @return Collection
     */
    public function getByCondition($condition);

    /**
     * Get slice of collection
     *
     * @param integer      $offset
     * @param integer|null $length
     * @return Collection
     */
    public function getSlice($offset, $length = null);

    /**
     * Get property from models
     *
     * @param string $name
     * @return array
     */
    public function getProperty($name);

    /**
     * Get property pairs
     *
     * @param string $keyPropertyName
     * @param string $valuePropertyName
     * @return array
     */
    public function getPropertyPairs($keyPropertyName, $valuePropertyName);

    /**
     * Remove model from collection
     *
     * @param SchemalessModelInterface $model
     * @return CollectionInterface|Collection
     */
    public function remove(SchemalessModelInterface $model);

    /**
     * Remove models by callback
     *
     * @param callable $callback
     * @return Collection
     */
    public function removeByCallback($callback);

    /**
     * Remove models by condition
     *
     * @param mixed $condition
     * @return Collection
     */
    public function removeByCondition($condition);


    /**
     * Shuffle collection
     *
     * @return CollectionInterface|Collection
     */
    public function shuffle();

    /**
     * Reverse collection
     *
     * @return CollectionInterface|Collection
     */
    public function reverse();

    /**
     * Clear collection
     *
     * @return CollectionInterface|Collection
     */
    public function clear();

    /**
     * Is empty?
     *
     * @return bool
     */
    public function isEmpty();

    /**
     * To array
     *
     * @param integer $depth
     * @return array
     */
    public function toArray($depth = 0);

    /**
     * Sort collection by callback
     *
     * @param callable $callback
     * @return Collection
     */
    public function sortByCallback($callback);

    /**
     * Sort collection
     *
     * @param array $propertyNames
     * @return Collection
     */
    public function sort(array $propertyNames);
}