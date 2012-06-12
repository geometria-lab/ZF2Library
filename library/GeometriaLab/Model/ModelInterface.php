<?php

namespace GeometriaLab\Model;

interface ModelInterface extends Schemaless\ModelInterface
{
    /**
     * Get definition
     *
     * @return Definition\DefinitionInterface
     */
    public function getDefinition();

    /**
     * Create model definition
     *
     * @static
     * @return Definition\DefinitionInterface
     */
    static public function createDefinition();
}