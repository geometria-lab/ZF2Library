<?php

namespace GeometriaLab\Model;

interface ModelInterface extends Schemaless\ModelInterface
{
    /**
     * Create model schema
     *
     * @static
     * @return Schema
     */
    static public function getSchema();
}