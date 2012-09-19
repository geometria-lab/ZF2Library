<?php

namespace GeometriaLab\Model;

use GeometriaLab\Model\Schema\Schema;

interface ModelInterface extends Schemaless\ModelInterface
{
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