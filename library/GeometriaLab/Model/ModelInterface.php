<?php

namespace GeometriaLab\Model;

interface ModelInterface extends Schemaless\ModelInterface
{
    /**
     * Get schema
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