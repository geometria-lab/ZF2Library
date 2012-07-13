<?php

namespace GeometriaLab\Model\Schemaless;

interface ModelInterface
{
    /**
     * Populate model from array or iterable object
     *
     * @param array|\Traversable|\stdClass $data  Model data (must be array or iterable object)
     * @return ModelInterface
     * @throws \InvalidArgumentException
     */
    public function populate($data);

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
     * @return ModelInterface
     */
    public function set($name, $value);

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
}