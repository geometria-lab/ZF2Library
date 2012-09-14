<?php

namespace GeometriaLab\Model;

use GeometriaLab\Model\Schema\Schema;

interface ModelInterface extends Schemaless\ModelInterface
{
    /**
     * Populate model from array or iterable object
     *
     * @param array|\Traversable|\stdClass $data  Model data (must be array or iterable object)
     * @return AbstractModel
     * @throws \InvalidArgumentException
     */
    public function populateSilent($data);

    /**
     * Set property value without throwing exception on validation
     *
     * @param string $name
     * @param mixed $value
     * @return AbstractModel|ModelInterface
     * @throws \InvalidArgumentException
     */
    public function setSilent($name, $value);

    public function isValid();

    public function getErrorMessages();

    /**
     * Create model schema
     *
     * @static
     * @return Schema
     */
    static public function getSchema();
}