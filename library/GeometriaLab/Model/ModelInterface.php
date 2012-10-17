<?php

namespace GeometriaLab\Model;

use GeometriaLab\Model\Schema\Schema;

interface ModelInterface
{
    /**
     * Validate model
     *
     * @return bool
     */
    public function isValid();

    /**
     * Get array with validation errors
     *
     * @return array
     */
    public function getErrorMessages();

    /**
     * Populate model from array or iterable object
     *
     * @param array|\Traversable|\stdClass $data  Model data (must be array or iterable object)
     * @param bool $notValidate
     * @return ModelInterface
     * @throws \InvalidArgumentException
     */
    public function populate($data, $notValidate = false);

    /**
     * Get property value
     *
     * @param string $name
     * @return mixed
     */
    public function get($name);

    /**
     * Set property
     *
     * @param string $name
     * @param mixed $value
     * @param bool $notValidate
     * @return ModelInterface
     */
    public function set($name, $value, $notValidate = false);

    /**
     * Has property
     *
     * @param string $name
     * @return bool
     */
    public function has($name);

    /**
     * Convert model to array
     *
     * @param integer $depth
     * @return array
     */
    public function toArray($depth = 0);

    /**
     * Create model schema
     *
     * @static
     * @return Schema
     */
    static public function getSchema();
}