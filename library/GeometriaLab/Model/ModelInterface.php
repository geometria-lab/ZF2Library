<?php

namespace GeometriaLab\Model;

interface ModelInterface extends Schemaless\ModelInterface
{
    /**
     * Get definition
     *
     * @return Schema
     */
    public function getSchema();

    /**
     * Create model schema
     *
     * @static
     * @return Schema
     */
    static public function createSchema();
}