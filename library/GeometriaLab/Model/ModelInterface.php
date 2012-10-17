<?php

namespace GeometriaLab\Model;

use GeometriaLab\Model\Schema\Schema;

interface ModelInterface extends Schemaless\ModelInterface
{
    /**
     * Is it valid?
     *
     * @return bool
     */
    public function isValid();

    /**
     * Get array with error messages
     *
     * @return array
     */
    public function getErrorMessages();

    /**
     * Set property value
     *
     * @param string $name
     * @param mixed $value
     * @return AbstractModel|ModelInterface
     * @throws \InvalidArgumentException
     */
    public function setWithoutValidation($name, $value);

    /**
     * Populate model from array or iterable object and doesn't validate it
     *
     * @param array|\Traversable|\stdClass $data  Model data (must be array or iterable object)
     * @return AbstractModel
     * @throws \InvalidArgumentException
     */
    public function populateWithoutValidation($data);

    /**
     * Create model schema
     *
     * @static
     * @return Schema
     */
    static public function getSchema();
}